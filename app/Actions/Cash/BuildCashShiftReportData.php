<?php

declare(strict_types=1);

namespace App\Actions\Cash;

use App\Models\CashShift;
use Illuminate\Support\Number;

final class BuildCashShiftReportData
{
    /** @return array<string, mixed> */
    public function execute(CashShift $shift): array
    {
        $shift->loadMissing(['hotel', 'reconciliations']);
        $locale = app()->getLocale();

        return [
            'code' => 'TR-' . str_pad((string) $shift->number, 6, '0', STR_PAD_LEFT),
            'number' => $shift->number,
            'hotel' => $shift->hotel->only(['business_name', 'tin', 'address', 'phone', 'mobile', 'email']),
            'opened_at' => $shift->opened_at->locale($locale)->translatedFormat('d M Y, H:i'),
            'closed_at' => $shift->closed_at?->locale($locale)->translatedFormat('d M Y, H:i'),
            'opening_note' => $shift->opening_note,
            'closing_note' => $shift->closing_note,
            'rows' => $shift->reconciliations->map(fn ($row): array => [
                'method' => $row->payment_method->value,
                'currency' => $row->currency,
                'opening' => $this->money($row->opening_minor, $row->currency),
                'inflows' => $this->money($row->inflow_minor, $row->currency),
                'outflows' => $this->money($row->outflow_minor, $row->currency),
                'expected' => $this->money($row->expected_closing_minor, $row->currency),
                'declared' => $this->money($row->declared_closing_minor, $row->currency),
                'difference' => $this->money($row->difference_minor, $row->currency),
            ])->all(),
        ];
    }

    private function money(int $minor, string $currency): string
    {
        return Number::currency($minor / 100, in: $currency, locale: app()->getLocale() === 'es' ? 'es_CO' : 'en_US');
    }
}
