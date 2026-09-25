<?php

declare(strict_types=1);

namespace App\Actions\Stays;

use App\Constants\HousekeepingStatus;
use App\Constants\LodgingChargePolicy;
use App\Constants\RoomOccupancyEndReason;
use App\Constants\RoomOccupancyEventType;
use App\Constants\StayStatus;
use App\Models\RoomOccupancy;
use App\Models\Stay;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CheckOutRoomOccupancy
{
    public function __construct(private RoomOccupancySnapshot $roomOccupancySnapshot) {}

    public function execute(Stay $stay, RoomOccupancy $roomOccupancy, CarbonImmutable $checkedOutAt, null|int $userId = null): RoomOccupancy
    {
        return Cache::lock("hotels:{$stay->hotel_id}:inventory", 10)->block(5, fn (): RoomOccupancy => DB::transaction(function () use ($stay, $roomOccupancy, $checkedOutAt, $userId): RoomOccupancy {
            $stay = Stay::query()->lockForUpdate()->findOrFail($stay->id);

            if ($stay->status !== StayStatus::Active) {
                throw ValidationException::withMessages([
                    'checked_out_at' => trans('stays.validation.stay_closed'),
                ]);
            }

            $roomOccupancy = $stay->roomOccupancies()
                ->with(['room', 'guests:id'])
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

            $before = $this->roomOccupancySnapshot->execute($roomOccupancy);

            $roomOccupancy->update([
                'checked_out_at' => $checkedOutAt,
                'end_reason' => RoomOccupancyEndReason::CheckOut,
            ]);
            $roomOccupancy->room->update(['housekeeping_status' => HousekeepingStatus::Dirty]);
            $roomOccupancy->events()->create([
                'type' => RoomOccupancyEventType::CheckedOut,
                'user_id' => $userId,
                'before_data' => $before,
                'after_data' => [
                    ...$this->roomOccupancySnapshot->execute($roomOccupancy->refresh()),
                    'lodging_charge_policy' => LodgingChargePolicy::ConsumedNights->value,
                ],
            ]);

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
