<?php

declare(strict_types=1);

namespace App\Actions\Stays;

use App\Actions\Rooms\RoomAvailability;
use App\Constants\StayStatus;
use App\Models\Stay;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class UpdateExpectedCheckOut
{
    public function __construct(private RoomAvailability $roomAvailability) {}

    public function execute(Stay $stay, CarbonImmutable $expectedCheckOutOn): Stay
    {
        return Cache::lock("hotels:{$stay->hotel_id}:inventory", 10)->block(5, fn (): Stay => DB::transaction(function () use ($stay, $expectedCheckOutOn): Stay {
            $stay = Stay::query()->lockForUpdate()->findOrFail($stay->id);

            if ($stay->status !== StayStatus::Active) {
                throw ValidationException::withMessages([
                    'expected_check_out_on' => trans('stays.validation.stay_closed'),
                ]);
            }

            if ($expectedCheckOutOn->isAfter($stay->expected_check_out_on)) {
                $occupancies = $stay->roomOccupancies()->whereNull('checked_out_at')->with('room')->get();

                foreach ($occupancies as $occupancy) {
                    if ($this->roomAvailability->hasConfirmedReservationConflict(
                        $occupancy->room,
                        $stay->expected_check_out_on->toImmutable(),
                        $expectedCheckOutOn,
                    )) {
                        throw ValidationException::withMessages([
                            'expected_check_out_on' => trans('stays.validation.room_unavailable'),
                        ]);
                    }
                }
            }

            $stay->update(['expected_check_out_on' => $expectedCheckOutOn]);
            $stay->roomOccupancies()
                ->whereNull('checked_out_at')
                ->update(['expected_check_out_on' => $expectedCheckOutOn]);

            return $stay;
        }));
    }
}
