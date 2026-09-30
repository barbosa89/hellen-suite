<?php

declare(strict_types=1);

namespace App\Actions\Cash;

use App\Actions\Financial\ConvertAmountToMinor;
use App\Constants\CashMovementDirection;
use App\Constants\CashMovementType;
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
    ) {}

    public function execute(Hotel $hotel, CashMovementType $type, string $amount, string $comment, null|int $userId): CashMovement
    {
        return DB::transaction(function () use ($hotel, $type, $amount, $comment, $userId): CashMovement {
            $amountMinor = $this->convertAmountToMinor->execute($amount);
            $direction = $type === CashMovementType::ManualEntry
                ? CashMovementDirection::In
                : CashMovementDirection::Out;

            if ($direction === CashMovementDirection::Out) {
                $balance = (int) $hotel->cashMovements()
                    ->where('currency', $this->settings->currency)
                    ->selectRaw("COALESCE(SUM(CASE WHEN direction = 'in' THEN amount_minor ELSE -amount_minor END), 0) AS balance")
                    ->value('balance');

                if ($amountMinor > $balance) {
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
                'recorded_by_user_id' => $userId,
            ]);
        });
    }
}
