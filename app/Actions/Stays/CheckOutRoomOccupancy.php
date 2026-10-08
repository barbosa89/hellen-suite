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
    public function __construct(
        private RoomOccupancySnapshot $roomOccupancySnapshot,
        private PostLodgingCharge $postLodgingCharge,
        private CalculateFolioBalance $calculateFolioBalance,
    ) {}

    public function execute(Stay $stay, RoomOccupancy $roomOccupancy, CarbonImmutable $checkedOutAt, null|int $userId = null): RoomOccupancy
    {
        return Cache::lock("hotels:{$stay->hotel_id}:inventory", 10)->block(5, fn (): RoomOccupancy => DB::transaction(function () use ($stay, $roomOccupancy, $checkedOutAt, $userId): RoomOccupancy {
            $stay = Stay::query()->lockForUpdate()->findOrFail($stay->id);

            $roomOccupancy = $this->lockAndValidate($stay, $roomOccupancy, $checkedOutAt);

            $before = $this->roomOccupancySnapshot->execute($roomOccupancy);

            $roomOccupancy->update([
                'checked_out_at' => $checkedOutAt,
                'end_reason' => RoomOccupancyEndReason::CheckOut,
            ]);

            $this->closeSettledFolio($roomOccupancy, $checkedOutAt, $userId);
            $roomOccupancy->room->update(['housekeeping_status' => HousekeepingStatus::Dirty]);
            $this->recordCheckedOutEvent($roomOccupancy, $before, $userId);
            $this->closeDepartedStayGuests($stay, $roomOccupancy, $checkedOutAt);
            $this->closeStayIfFullyCheckedOut($stay, $checkedOutAt);

            return $roomOccupancy;
        }));
    }

    private function lockAndValidate(Stay $stay, RoomOccupancy $roomOccupancy, CarbonImmutable $checkedOutAt): RoomOccupancy
    {
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

        return $roomOccupancy;
    }

    private function closeSettledFolio(RoomOccupancy $roomOccupancy, CarbonImmutable $checkedOutAt, null|int $userId): void
    {
        $this->postLodgingCharge->execute($roomOccupancy->refresh(), $checkedOutAt, $userId);
        $folio = $roomOccupancy->folio()->with(['charges', 'adjustments', 'payments'])->firstOrFail();

        if ($this->calculateFolioBalance->execute($folio) !== 0) {
            throw ValidationException::withMessages([
                'payment' => trans('payments.validation.balance_due'),
            ]);
        }

        $folio->update([
            'closed_at' => $checkedOutAt,
            'closed_by_user_id' => $userId,
        ]);
    }

    /** @param array<string, mixed> $before */
    private function recordCheckedOutEvent(RoomOccupancy $roomOccupancy, array $before, null|int $userId): void
    {
        $roomOccupancy->events()->create([
            'type' => RoomOccupancyEventType::CheckedOut,
            'user_id' => $userId,
            'before_data' => $before,
            'after_data' => [
                ...$this->roomOccupancySnapshot->execute($roomOccupancy->refresh()),
                'lodging_charge_policy' => LodgingChargePolicy::ConsumedNights->value,
            ],
        ]);
    }

    private function closeDepartedStayGuests(Stay $stay, RoomOccupancy $roomOccupancy, CarbonImmutable $checkedOutAt): void
    {
        $departedGuestIds = $roomOccupancy->guests()->pluck('guests.id');

        $stillInsideGuestIds = DB::table('guest_room_occupancy')
            ->whereIn('room_occupancy_id', $stay->roomOccupancies()->whereNull('checked_out_at')->pluck('id'))
            ->pluck('guest_id');

        $stay->stayGuests()
            ->whereIn('guest_id', $departedGuestIds)
            ->whereNotIn('guest_id', $stillInsideGuestIds)
            ->whereNull('checked_out_at')
            ->update(['checked_out_at' => $checkedOutAt]);
    }

    private function closeStayIfFullyCheckedOut(Stay $stay, CarbonImmutable $checkedOutAt): void
    {
        if ($stay->roomOccupancies()->whereNull('checked_out_at')->exists()) {
            return;
        }

        $stay->update([
            'status' => StayStatus::CheckedOut,
            'checked_out_at' => $checkedOutAt,
        ]);
    }
}
