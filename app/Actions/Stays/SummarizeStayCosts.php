<?php

declare(strict_types=1);

namespace App\Actions\Stays;

use App\Actions\Financial\ConvertAmountToMinor;
use App\Models\RoomOccupancy;
use App\Models\Stay;
use Carbon\CarbonImmutable;

use function sprintf;

final class SummarizeStayCosts
{
    public function __construct(private ConvertAmountToMinor $convertAmountToMinor) {}

    /**
     * @return array{
     *     is_estimate: bool,
     *     total_nights: int,
     *     total_amount: string,
     *     final_nights: int,
     *     final_amount: string,
     *     estimated_nights: int,
     *     estimated_amount: string,
     *     items: array<int, array{
     *         room_occupancy_id: int,
     *         is_estimate: bool,
     *         billable_nights: int,
     *         nightly_rate: string,
     *         subtotal_amount: string,
     *         period_start_on: string,
     *         period_end_on: string,
     *         check_out_now_on: string,
     *         check_out_now_billable_nights: int,
     *         check_out_now_subtotal_amount: string
     *     }>
     * }
     */
    public function execute(Stay $stay): array
    {
        $totalNights = 0;
        $totalCents = 0;
        $finalNights = 0;
        $finalCents = 0;
        $estimatedNights = 0;
        $estimatedCents = 0;
        $items = [];

        foreach ($stay->roomOccupancies as $occupancy) {
            $item = $this->summarizeOccupancy($occupancy);

            $totalNights += $item['billable_nights'];
            $subtotalCents = $this->convertAmountToMinor->execute($item['subtotal_amount']);
            $totalCents += $subtotalCents;

            if ($item['is_estimate']) {
                $estimatedNights += $item['billable_nights'];
                $estimatedCents += $subtotalCents;
            } else {
                $finalNights += $item['billable_nights'];
                $finalCents += $subtotalCents;
            }

            $items[] = $item;
        }

        return [
            'is_estimate' => $estimatedNights > 0,
            'total_nights' => $totalNights,
            'total_amount' => $this->formatCents($totalCents),
            'final_nights' => $finalNights,
            'final_amount' => $this->formatCents($finalCents),
            'estimated_nights' => $estimatedNights,
            'estimated_amount' => $this->formatCents($estimatedCents),
            'items' => $items,
        ];
    }

    /**
     * @return array{
     *     room_occupancy_id: int,
     *     is_estimate: bool,
     *     billable_nights: int,
     *     nightly_rate: string,
     *     subtotal_amount: string,
     *     period_start_on: string,
     *     period_end_on: string,
     *     check_out_now_on: string,
     *     check_out_now_billable_nights: int,
     *     check_out_now_subtotal_amount: string
     * }
     */
    private function summarizeOccupancy(RoomOccupancy $occupancy): array
    {
        $periodStartOn = $occupancy->checked_in_at->toDateString();
        $periodEndOn = ($occupancy->checked_out_at ?? $occupancy->expected_check_out_on)->toDateString();
        $billableNights = $this->billableNights($periodStartOn, $periodEndOn);
        $nightlyRate = (string) $occupancy->nightly_rate;
        $nightlyRateCents = $this->convertAmountToMinor->execute($nightlyRate);
        $subtotalCents = $nightlyRateCents * $billableNights;
        $checkOutNowOn = $occupancy->checked_out_at?->toDateString() ?? today()->toDateString();
        $checkOutNowBillableNights = $this->billableNights($periodStartOn, $checkOutNowOn);

        return [
            'room_occupancy_id' => $occupancy->id,
            'is_estimate' => $occupancy->checked_out_at === null,
            'billable_nights' => $billableNights,
            'nightly_rate' => $nightlyRate,
            'subtotal_amount' => $this->formatCents($subtotalCents),
            'period_start_on' => $periodStartOn,
            'period_end_on' => $periodEndOn,
            'check_out_now_on' => $checkOutNowOn,
            'check_out_now_billable_nights' => $checkOutNowBillableNights,
            'check_out_now_subtotal_amount' => $this->formatCents($nightlyRateCents * $checkOutNowBillableNights),
        ];
    }

    private function billableNights(string $periodStartOn, string $periodEndOn): int
    {
        $startOn = CarbonImmutable::parse($periodStartOn)->startOfDay();
        $endOn = CarbonImmutable::parse($periodEndOn)->startOfDay();

        return max(1, (int) $startOn->diffInDays($endOn, false));
    }

    private function formatCents(int $cents): string
    {
        return sprintf('%d.%02d', intdiv($cents, 100), $cents % 100);
    }
}
