<?php

declare(strict_types=1);

namespace App\Actions\Stays;

use App\Models\RoomOccupancy;

final class RoomOccupancySnapshot
{
    /** @return array<string, mixed> */
    public function execute(RoomOccupancy $roomOccupancy): array
    {
        $roomOccupancy->loadMissing('guests:id');

        return [
            'room_id' => $roomOccupancy->room_id,
            'guest_ids' => $roomOccupancy->guests->modelKeys(),
            'nightly_rate' => $roomOccupancy->nightly_rate,
            'checked_in_at' => $roomOccupancy->checked_in_at?->toDateTimeString(),
            'expected_check_out_on' => $roomOccupancy->expected_check_out_on?->toDateString(),
            'checked_out_at' => $roomOccupancy->checked_out_at?->toDateTimeString(),
            'end_reason' => $roomOccupancy->end_reason?->value,
        ];
    }
}
