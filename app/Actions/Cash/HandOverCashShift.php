<?php

declare(strict_types=1);

namespace App\Actions\Cash;

use App\Constants\PaymentMethod;
use App\Models\CashShift;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\DB;

final class HandOverCashShift
{
    public function __construct(
        private CloseCashShift $closeCashShift,
        private OpenCashShift $openCashShift,
        private GeneralSettings $settings,
    ) {}

    /** @param array<int, array{method: string, currency: string, declared_amount: string}> $declarations */
    public function execute(CashShift $shift, array $declarations, null|string $closingNote = null, null|string $openingNote = null): CashShift
    {
        return DB::transaction(function () use ($shift, $declarations, $closingNote, $openingNote): CashShift {
            $closedShift = $this->closeCashShift->execute($shift, $declarations, $closingNote);
            $cash = $closedShift->reconciliations->first(
                fn ($row): bool => $row->payment_method === PaymentMethod::Cash && $row->currency === $this->settings->currency
            );
            $openingAmount = number_format(($cash?->declared_closing_minor ?? 0) / 100, 2, '.', '');

            $nextShift = $this->openCashShift->execute($closedShift->hotel, $openingAmount, $openingNote, $closedShift);

            foreach ($closedShift->reconciliations->where('payment_method', PaymentMethod::Cash) as $cashReconciliation) {
                $nextShift->reconciliations()->updateOrCreate(
                    ['payment_method' => PaymentMethod::Cash, 'currency' => $cashReconciliation->currency],
                    ['opening_minor' => $cashReconciliation->declared_closing_minor],
                );
            }

            return $nextShift;
        });
    }
}
