<?php

declare(strict_types=1);

namespace App\Actions\Payments;

use App\Actions\Financial\ConvertAmountToMinor;
use App\Actions\Stays\SummarizeStayFolio;
use App\Constants\CashMovementDirection;
use App\Constants\CashMovementType;
use App\Constants\PaymentMethod;
use App\Constants\PaymentType;
use App\Models\Payment;
use App\Models\StayFolio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class RecordPayment
{
    public function __construct(
        private ConvertAmountToMinor $convertAmountToMinor,
        private SummarizeStayFolio $summarizeStayFolio,
    ) {}

    public function execute(StayFolio $folio, string $amount, PaymentMethod $method, null|string $comment, null|string $supportPath, null|int $userId): Payment
    {
        return DB::transaction(function () use ($folio, $amount, $method, $comment, $supportPath, $userId): Payment {
            $folio = StayFolio::query()->with('stay')->lockForUpdate()->findOrFail($folio->id);

            if ($folio->closed_at !== null) {
                throw ValidationException::withMessages(['payment' => trans('payments.validation.folio_closed')]);
            }

            $amountMinor = $this->convertAmountToMinor->execute($amount);
            $summary = $this->summarizeStayFolio->execute($folio->stay);
            $folioSummary = collect($summary['folios'])->firstWhere('id', $folio->id);

            if ($amountMinor > $folioSummary['balance_minor']) {
                throw ValidationException::withMessages(['amount' => trans('payments.validation.overpayment')]);
            }

            $payment = $folio->payments()->create([
                'type' => PaymentType::Receipt,
                'method' => $method,
                'amount_minor' => $amountMinor,
                'currency' => $folio->currency,
                'comment' => $comment,
                'support_path' => $supportPath,
                'paid_at' => now(),
                'idempotency_key' => (string) Str::uuid(),
                'recorded_by_user_id' => $userId,
            ]);

            if ($method === PaymentMethod::Cash) {
                $folio->hotel->cashMovements()->create([
                    'type' => CashMovementType::StayPayment,
                    'direction' => CashMovementDirection::In,
                    'amount_minor' => $amountMinor,
                    'currency' => $folio->currency,
                    'comment' => $comment,
                    'occurred_at' => now(),
                    'idempotency_key' => (string) Str::uuid(),
                    'payment_id' => $payment->id,
                    'recorded_by_user_id' => $userId,
                ]);
            }

            return $payment;
        });
    }
}
