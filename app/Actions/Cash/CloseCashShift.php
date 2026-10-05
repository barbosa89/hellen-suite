<?php

declare(strict_types=1);

namespace App\Actions\Cash;

use App\Actions\Financial\ConvertAmountToMinor;
use App\Constants\PaymentMethod;
use App\Models\CashShift;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CloseCashShift
{
    public function __construct(
        private SummarizeCashShift $summarizeCashShift,
        private ConvertAmountToMinor $convertAmountToMinor,
    ) {}

    /** @param array<int, array{method: string, currency: string, declared_amount: string}> $declarations */
    public function execute(CashShift $shift, array $declarations, null|string $note = null): CashShift
    {
        return DB::transaction(function () use ($shift, $declarations, $note): CashShift {
            $shift = CashShift::query()->with('reconciliations')->lockForUpdate()->findOrFail($shift->id);

            if ($shift->closed_at !== null) {
                throw ValidationException::withMessages(['cash_shift' => trans('cash.validation.shift_closed')]);
            }

            $declared = collect($declarations)->keyBy(fn (array $row): string => "{$row['method']}|{$row['currency']}");
            $summary = $this->summarizeCashShift->execute($shift);

            if ($declared->count() !== count($summary)) {
                throw ValidationException::withMessages(['reconciliations' => trans('cash.validation.incomplete_reconciliation')]);
            }

            foreach ($summary as $row) {
                $key = "{$row['method']}|{$row['currency']}";
                $declaration = $declared->get($key);
                if ($declaration === null) {
                    throw ValidationException::withMessages(['reconciliations' => trans('cash.validation.incomplete_reconciliation')]);
                }

                $declaredMinor = $this->convertAmountToMinor->execute($declaration['declared_amount']);
                if ($row['method'] === PaymentMethod::Cash->value && $declaredMinor < 0) {
                    throw ValidationException::withMessages(['reconciliations' => trans('cash.validation.negative_cash')]);
                }
                $shift->reconciliations()->updateOrCreate(
                    ['payment_method' => $row['method'], 'currency' => $row['currency']],
                    [
                        'opening_minor' => $row['opening_minor'],
                        'inflow_minor' => $row['inflow_minor'],
                        'outflow_minor' => $row['outflow_minor'],
                        'expected_closing_minor' => $row['expected_closing_minor'],
                        'declared_closing_minor' => $declaredMinor,
                        'difference_minor' => $declaredMinor - $row['expected_closing_minor'],
                    ],
                );
            }

            $shift->update(['closed_at' => now(), 'closing_note' => $note]);

            return $shift->fresh(['reconciliations']);
        }, attempts: 5);
    }
}
