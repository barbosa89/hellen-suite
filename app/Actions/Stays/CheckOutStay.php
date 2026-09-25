<?php

declare(strict_types=1);

namespace App\Actions\Stays;

use App\Constants\HousekeepingStatus;
use App\Constants\LodgingChargePolicy;
use App\Constants\RoomOccupancyEndReason;
use App\Constants\RoomOccupancyEventType;
use App\Constants\StayStatus;
use App\Models\Stay;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CheckOutStay
{
    public function __construct(private RoomOccupancySnapshot $roomOccupancySnapshot) {}

    public function execute(Stay $stay, CarbonImmutable $checkedOutAt, null|int $userId = null): Stay
    {
        return DB::transaction(function () use ($stay, $checkedOutAt, $userId): Stay {
            $stay = Stay::query()->lockForUpdate()->findOrFail($stay->id);

            if ($stay->status !== StayStatus::Active) {
                throw ValidationException::withMessages([
                    'checked_out_at' => trans('stays.validation.stay_closed'),
                ]);
            }

            $occupancies = $stay->roomOccupancies()
                ->whereNull('checked_out_at')
                ->with(['room', 'guests:id'])
                ->lockForUpdate()
                ->get();

            foreach ($occupancies as $occupancy) {
                $before = $this->roomOccupancySnapshot->execute($occupancy);

                $occupancy->update([
                    'checked_out_at' => $checkedOutAt,
                    'end_reason' => RoomOccupancyEndReason::CheckOut,
                ]);

                $occupancy->room->update(['housekeeping_status' => HousekeepingStatus::Dirty]);
                $occupancy->events()->create([
                    'type' => RoomOccupancyEventType::CheckedOut,
                    'user_id' => $userId,
                    'before_data' => $before,
                    'after_data' => [
                        ...$this->roomOccupancySnapshot->execute($occupancy->refresh()),
                        'lodging_charge_policy' => LodgingChargePolicy::ConsumedNights->value,
                    ],
                ]);
            }

            $stay->update([
                'status' => StayStatus::CheckedOut,
                'checked_out_at' => $checkedOutAt,
            ]);

            return $stay;
        });
    }
}
