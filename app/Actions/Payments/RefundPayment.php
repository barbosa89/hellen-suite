<?php

declare(strict_types=1);

namespace App\Actions\Payments;

use App\Actions\Cash\GetOpenCashShift;
use App\Actions\Financial\ConvertAmountToMinor;
use App\Constants\CashMovementDirection;
use App\Constants\CashMovementType;
use App\Constants\PaymentMethod;
use App\Constants\PaymentType;
use App\Models\Payment;
use App\Models\StayFolio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class RefundPayment
{
    public function __construct(
        private ConvertAmountToMinor $convertAmountToMinor,
        private GetOpenCashShift $getOpenCashShift,
    ) {}

    public function execute(Payment $payment, string $amount, string $reason, null|int $userId): Payment
    {
        return DB::transaction(function () use ($payment, $amount, $reason, $userId): Payment {
            $payment = Payment::query()->with('refunds')->lockForUpdate()->findOrFail($payment->id);
            $folio = StayFolio::query()->with('hotel')->lockForUpdate()->findOrFail($payment->stay_folio_id);
            $shift = $this->getOpenCashShift->execute($folio->hotel, lock: true);

            if ($folio->closed_at !== null) {
                throw ValidationException::withMessages(['payment' => trans('payments.validation.folio_closed')]);
            }

            $amountMinor = $this->convertAmountToMinor->execute($amount);
            $refundableMinor = $payment->amount_minor - $payment->refunds->sum('amount_minor');

            if ($payment->type !== PaymentType::Receipt || $amountMinor > $refundableMinor) {
                throw ValidationException::withMessages(['amount' => trans('payments.validation.refund_exceeds_payment')]);
            }

            if ($payment->method === PaymentMethod::Cash) {
                $openingMinor = (int) $shift->reconciliations()
                    ->where('payment_method', PaymentMethod::Cash)
                    ->where('currency', $payment->currency)
                    ->value('opening_minor');
                $activityMinor = (int) $shift->cashMovements()
                    ->where('currency', $payment->currency)
                    ->selectRaw("COALESCE(SUM(CASE WHEN direction = 'in' THEN amount_minor ELSE -amount_minor END), 0) AS balance")
                    ->value('balance');

                if ($amountMinor > $openingMinor + $activityMinor) {
                    throw ValidationException::withMessages(['amount' => trans('cash.validation.insufficient_balance')]);
                }
            }

            $refund = $folio->payments()->create([
                'type' => PaymentType::Refund,
                'method' => $payment->method,
                'amount_minor' => $amountMinor,
                'currency' => $payment->currency,
                'comment' => $reason,
                'paid_at' => now(),
                'idempotency_key' => (string) Str::uuid(),
                'parent_payment_id' => $payment->id,
                'recorded_by_user_id' => $userId,
                'cash_shift_id' => $shift->id,
            ]);

            if ($payment->method === PaymentMethod::Cash) {
                $folio->hotel->cashMovements()->create([
                    'type' => CashMovementType::PaymentRefund,
                    'direction' => CashMovementDirection::Out,
                    'amount_minor' => $amountMinor,
                    'currency' => $payment->currency,
                    'comment' => $reason,
                    'occurred_at' => now(),
                    'idempotency_key' => (string) Str::uuid(),
                    'payment_id' => $refund->id,
                    'recorded_by_user_id' => $userId,
                    'cash_shift_id' => $shift->id,
                ]);
            }

            return $refund;
        });
    }
}
