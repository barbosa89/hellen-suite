<?php

declare(strict_types=1);

namespace App\Actions\Stays;

use App\Constants\HousekeepingStatus;
use App\Constants\RoomOccupancyEndReason;
use App\Models\Hotel;
use App\Models\RoomOccupancy;
use App\Models\Stay;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class TransferRoomOccupancy
{
    public function execute(Hotel $hotel, Stay $stay, RoomOccupancy $occupancy, int $roomId, string $nightlyRate): RoomOccupancy
    {
        return DB::transaction(function () use ($hotel, $stay, $occupancy, $roomId, $nightlyRate): RoomOccupancy {
            $occupancy = $stay->roomOccupancies()
                ->with(['room', 'guests'])
                ->lockForUpdate()
                ->findOrFail($occupancy->id);

            if ($occupancy->checked_out_at !== null) {
                throw ValidationException::withMessages([
                    'room_id' => trans('stays.validation.occupancy_closed'),
                ]);
            }

            $newRoom = $hotel->rooms()
                ->with('roomType:id,capacity')
                ->lockForUpdate()
                ->findOrFail($roomId);

            $isUnavailable = $newRoom->id === $occupancy->room_id
                || ! $newRoom->is_active
                || $newRoom->housekeeping_status !== HousekeepingStatus::Clean
                || $newRoom->roomOccupancies()->whereNull('checked_out_at')->exists();

            if ($isUnavailable || $occupancy->guests->count() > $newRoom->roomType->capacity) {
                throw ValidationException::withMessages([
                    'room_id' => trans('stays.validation.room_unavailable'),
                ]);
            }

            $transferredAt = now();
            $occupancy->update([
                'checked_out_at' => $transferredAt,
                'end_reason' => RoomOccupancyEndReason::Transfer,
            ]);
            $occupancy->room->update(['housekeeping_status' => HousekeepingStatus::Dirty]);

            $newOccupancy = $stay->roomOccupancies()->create([
                'room_id' => $newRoom->id,
                'nightly_rate' => $nightlyRate,
                'checked_in_at' => $transferredAt,
                'expected_check_out_on' => $stay->expected_check_out_on,
            ]);

            $newOccupancy->guests()->attach($occupancy->guests->modelKeys());

            return $newOccupancy;
        });
    }
}
