<?php

declare(strict_types=1);

namespace App\Actions\Stays;

use App\Constants\StayStatus;
use App\Models\RoomOccupancy;
use App\Models\Stay;
use Carbon\CarbonImmutable;

use function sprintf;

final class SummarizeStayCosts
{
    /**
     * @return array{
     *     is_estimate: bool,
     *     total_nights: int,
     *     total_amount: string,
     *     items: array<int, array{
     *         room_occupancy_id: int,
     *         billable_nights: int,
     *         nightly_rate: string,
     *         subtotal_amount: string,
     *         period_start_on: string,
     *         period_end_on: string
     *     }>
     * }
     */
    public function execute(Stay $stay): array
    {
        $totalNights = 0;
        $totalCents = 0;
        $items = [];

        foreach ($stay->roomOccupancies as $occupancy) {
            $item = $this->summarizeOccupancy($occupancy);

            $totalNights += $item['billable_nights'];
            $totalCents += $this->decimalToCents($item['subtotal_amount']);
            $items[] = $item;
        }

        return [
            'is_estimate' => $stay->status === StayStatus::Active,
            'total_nights' => $totalNights,
            'total_amount' => $this->formatCents($totalCents),
            'items' => $items,
        ];
    }

    /**
     * @return array{
     *     room_occupancy_id: int,
     *     billable_nights: int,
     *     nightly_rate: string,
     *     subtotal_amount: string,
     *     period_start_on: string,
     *     period_end_on: string
     * }
     */
    private function summarizeOccupancy(RoomOccupancy $occupancy): array
    {
        $periodStartOn = $occupancy->checked_in_at->toDateString();
        $periodEndOn = ($occupancy->checked_out_at ?? $occupancy->expected_check_out_on)->toDateString();
        $billableNights = $this->billableNights($periodStartOn, $periodEndOn);
        $nightlyRate = (string) $occupancy->nightly_rate;
        $subtotalCents = $this->decimalToCents($nightlyRate) * $billableNights;

        return [
            'room_occupancy_id' => $occupancy->id,
            'billable_nights' => $billableNights,
            'nightly_rate' => $nightlyRate,
            'subtotal_amount' => $this->formatCents($subtotalCents),
            'period_start_on' => $periodStartOn,
            'period_end_on' => $periodEndOn,
        ];
    }

    private function billableNights(string $periodStartOn, string $periodEndOn): int
    {
        $startOn = CarbonImmutable::parse($periodStartOn)->startOfDay();
        $endOn = CarbonImmutable::parse($periodEndOn)->startOfDay();

        return max(1, (int) $startOn->diffInDays($endOn, false));
    }

    private function decimalToCents(string $amount): int
    {
        [$whole, $decimal] = array_pad(explode('.', $amount, 2), 2, '0');

        return ((int) $whole * 100) + (int) str_pad(substr($decimal, 0, 2), 2, '0');
    }

    private function formatCents(int $cents): string
    {
        return sprintf('%d.%02d', intdiv($cents, 100), $cents % 100);
    }
}
