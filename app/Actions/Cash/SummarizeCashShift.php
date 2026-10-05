<?php

declare(strict_types=1);

namespace App\Actions\Cash;

use App\Constants\CashMovementDirection;
use App\Constants\PaymentMethod;
use App\Constants\PaymentType;
use App\Models\CashShift;

final class SummarizeCashShift
{
    /** @return array<int, array<string, int|string|null>> */
    public function execute(CashShift $shift): array
    {
        $rows = [];

        foreach ($shift->reconciliations as $reconciliation) {
            $key = $this->key($reconciliation->payment_method->value, $reconciliation->currency);
            $rows[$key] = [
                'method' => $reconciliation->payment_method->value,
                'currency' => $reconciliation->currency,
                'opening_minor' => $reconciliation->opening_minor,
                'inflow_minor' => 0,
                'outflow_minor' => 0,
                'expected_closing_minor' => null,
                'declared_closing_minor' => $reconciliation->declared_closing_minor,
                'difference_minor' => $reconciliation->difference_minor,
            ];
        }

        foreach ($shift->cashMovements()->get() as $movement) {
            $key = $this->key(PaymentMethod::Cash->value, $movement->currency);
            $rows[$key] ??= $this->emptyRow(PaymentMethod::Cash->value, $movement->currency);
            $field = $movement->direction === CashMovementDirection::In ? 'inflow_minor' : 'outflow_minor';
            $rows[$key][$field] += $movement->amount_minor;
        }

        foreach ($shift->payments()->where('method', PaymentMethod::BankTransfer)->get() as $payment) {
            $key = $this->key(PaymentMethod::BankTransfer->value, $payment->currency);
            $rows[$key] ??= $this->emptyRow(PaymentMethod::BankTransfer->value, $payment->currency);
            $field = $payment->type === PaymentType::Receipt ? 'inflow_minor' : 'outflow_minor';
            $rows[$key][$field] += $payment->amount_minor;
        }

        foreach ($rows as $key => $row) {
            $expectedClosing = $row['opening_minor'] + $row['inflow_minor'] - $row['outflow_minor'];
            $rows[$key]['expected_closing_minor'] = $expectedClosing;

            if ($row['declared_closing_minor'] !== null) {
                $rows[$key]['difference_minor'] = $row['declared_closing_minor'] - $expectedClosing;
            }
        }

        return array_values($rows);
    }

    /** @return array<string, int|string|null> */
    private function emptyRow(string $method, string $currency): array
    {
        return [
            'method' => $method,
            'currency' => $currency,
            'opening_minor' => 0,
            'inflow_minor' => 0,
            'outflow_minor' => 0,
            'expected_closing_minor' => null,
            'declared_closing_minor' => null,
            'difference_minor' => null,
        ];
    }

    private function key(string $method, string $currency): string
    {
        return "{$method}|{$currency}";
    }
}
