<?php

declare(strict_types=1);

namespace App\Actions\Stays;

use App\Actions\Financial\ConvertAmountToMinor;
use App\Constants\RoomOccupancyEndReason;
use App\Models\RoomOccupancy;
use Carbon\CarbonImmutable;

final class CalculateOccupancyCharge
{
    public function __construct(private ConvertAmountToMinor $convertAmountToMinor) {}

    /** @return array{nights: int, unit_amount_minor: int, total_amount_minor: int, start_on: string, end_on: string} */
    public function execute(RoomOccupancy $occupancy, null|CarbonImmutable $effectiveCheckOutAt = null): array
    {
        $startOn = CarbonImmutable::parse($occupancy->checked_in_at->toDateString());
        $endOn = CarbonImmutable::parse(($effectiveCheckOutAt ?? $occupancy->checked_out_at ?? $occupancy->expected_check_out_on)->toDateString());
        $nights = max(0, (int) $startOn->diffInDays($endOn, false));

        if ($nights === 0 && $occupancy->end_reason !== RoomOccupancyEndReason::Transfer && ! $this->folioAlreadyConsumedNight($occupancy)) {
            $nights = 1;
        }

        $unitAmountMinor = $this->convertAmountToMinor->execute((string) $occupancy->nightly_rate);

        return [
            'nights' => $nights,
            'unit_amount_minor' => $unitAmountMinor,
            'total_amount_minor' => $unitAmountMinor * $nights,
            'start_on' => $startOn->toDateString(),
            'end_on' => $endOn->toDateString(),
        ];
    }

    private function folioAlreadyConsumedNight(RoomOccupancy $occupancy): bool
    {
        if ($occupancy->stay_folio_id === null) {
            return false;
        }

        return RoomOccupancy::query()
            ->where('stay_folio_id', $occupancy->stay_folio_id)
            ->where('id', '!=', $occupancy->id)
            ->whereNotNull('checked_out_at')
            ->get(['checked_in_at', 'checked_out_at'])
            ->contains(fn (RoomOccupancy $segment): bool => $segment->checked_in_at->startOfDay()->diffInDays($segment->checked_out_at->startOfDay(), false) > 0);
    }
}
