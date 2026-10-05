<?php

declare(strict_types=1);

namespace App\Actions\Cash;

use App\Actions\Financial\ConvertAmountToMinor;
use App\Constants\CashMovementDirection;
use App\Constants\CashMovementType;
use App\Constants\PaymentMethod;
use App\Models\CashMovement;
use App\Models\Hotel;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class RecordManualCashMovement
{
    public function __construct(
        private ConvertAmountToMinor $convertAmountToMinor,
        private GeneralSettings $settings,
        private GetOpenCashShift $getOpenCashShift,
    ) {}

    public function execute(Hotel $hotel, CashMovementType $type, string $amount, string $comment): CashMovement
    {
        return DB::transaction(function () use ($hotel, $type, $amount, $comment): CashMovement {
            $shift = $this->getOpenCashShift->execute($hotel, lock: true);
            $amountMinor = $this->convertAmountToMinor->execute($amount);
            $direction = $type === CashMovementType::ManualEntry
                ? CashMovementDirection::In
                : CashMovementDirection::Out;

            if ($direction === CashMovementDirection::Out) {
                $opening = (int) $shift->reconciliations()
                    ->where('payment_method', PaymentMethod::Cash)
                    ->where('currency', $this->settings->currency)
                    ->value('opening_minor');
                $activity = (int) $shift->cashMovements()
                    ->where('currency', $this->settings->currency)
                    ->selectRaw("COALESCE(SUM(CASE WHEN direction = 'in' THEN amount_minor ELSE -amount_minor END), 0) AS balance")
                    ->value('balance');

                if ($amountMinor > $opening + $activity) {
                    throw ValidationException::withMessages(['amount' => trans('cash.validation.insufficient_balance')]);
                }
            }

            return $hotel->cashMovements()->create([
                'type' => $type,
                'direction' => $direction,
                'amount_minor' => $amountMinor,
                'currency' => $this->settings->currency,
                'comment' => $comment,
                'occurred_at' => now(),
                'idempotency_key' => (string) Str::uuid(),
                'cash_shift_id' => $shift->id,
            ]);
        });
    }
}
