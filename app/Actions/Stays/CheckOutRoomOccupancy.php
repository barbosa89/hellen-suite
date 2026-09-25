<?php

declare(strict_types=1);

namespace App\Actions\Stays;

use App\Constants\HousekeepingStatus;
use App\Constants\RoomOccupancyEndReason;
use App\Constants\StayStatus;
use App\Models\RoomOccupancy;
use App\Models\Stay;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CheckOutRoomOccupancy
{
    public function execute(Stay $stay, RoomOccupancy $roomOccupancy, CarbonImmutable $checkedOutAt): RoomOccupancy
    {
        return Cache::lock("hotels:{$stay->hotel_id}:inventory", 10)->block(5, fn (): RoomOccupancy => DB::transaction(function () use ($stay, $roomOccupancy, $checkedOutAt): RoomOccupancy {
            $stay = Stay::query()->lockForUpdate()->findOrFail($stay->id);

            if ($stay->status !== StayStatus::Active) {
                throw ValidationException::withMessages([
                    'checked_out_at' => trans('stays.validation.stay_closed'),
                ]);
            }

            $roomOccupancy = $stay->roomOccupancies()
                ->with('room')
                ->lockForUpdate()
                ->findOrFail($roomOccupancy->id);

            if ($roomOccupancy->checked_out_at !== null) {
                throw ValidationException::withMessages([
                    'checked_out_at' => trans('stays.validation.occupancy_closed'),
                ]);
            }

            if ($checkedOutAt->isBefore($roomOccupancy->checked_in_at)) {
                throw ValidationException::withMessages([
                    'checked_out_at' => trans('stays.validation.check_out_before_check_in'),
                ]);
            }

            if ($checkedOutAt->isFuture()) {
                throw ValidationException::withMessages([
                    'checked_out_at' => trans('stays.validation.check_out_future'),
                ]);
            }

            $roomOccupancy->update([
                'checked_out_at' => $checkedOutAt,
                'end_reason' => RoomOccupancyEndReason::CheckOut,
            ]);
            $roomOccupancy->room->update(['housekeeping_status' => HousekeepingStatus::Dirty]);

            if (! $stay->roomOccupancies()->whereNull('checked_out_at')->exists()) {
                $stay->update([
                    'status' => StayStatus::CheckedOut,
                    'checked_out_at' => $checkedOutAt,
                ]);
            }

            return $roomOccupancy;
        }));
    }
}
