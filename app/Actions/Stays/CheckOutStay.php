<?php

declare(strict_types=1);

namespace App\Actions\Stays;

use App\Constants\HousekeepingStatus;
use App\Constants\RoomOccupancyEndReason;
use App\Constants\StayStatus;
use App\Models\Stay;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CheckOutStay
{
    public function execute(Stay $stay, CarbonImmutable $checkedOutAt): Stay
    {
        return DB::transaction(function () use ($stay, $checkedOutAt): Stay {
            $stay = Stay::query()->lockForUpdate()->findOrFail($stay->id);

            if ($stay->status !== StayStatus::Active) {
                throw ValidationException::withMessages([
                    'checked_out_at' => trans('stays.validation.stay_closed'),
                ]);
            }

            $occupancies = $stay->roomOccupancies()
                ->whereNull('checked_out_at')
                ->with('room')
                ->get();

            foreach ($occupancies as $occupancy) {
                $occupancy->update([
                    'checked_out_at' => $checkedOutAt,
                    'end_reason' => RoomOccupancyEndReason::CheckOut,
                ]);

                $occupancy->room->update(['housekeeping_status' => HousekeepingStatus::Dirty]);
            }

            $stay->update([
                'status' => StayStatus::CheckedOut,
                'checked_out_at' => $checkedOutAt,
            ]);

            return $stay;
        });
    }
}
