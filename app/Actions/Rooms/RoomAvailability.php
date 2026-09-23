<?php

declare(strict_types=1);

namespace App\Actions\Rooms;

use App\Constants\HousekeepingStatus;
use App\Constants\ReservationStatus;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\Room;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;

final class RoomAvailability
{
    /** @return Builder<Room> */
    public function query(Hotel $hotel, CarbonImmutable $checkInOn, CarbonImmutable $checkOutOn, null|Reservation $exceptReservation = null): Builder
    {
        return $hotel->rooms()
            ->getQuery()
            ->where('is_active', true)
            ->where('housekeeping_status', HousekeepingStatus::Clean)
            ->whereDoesntHave('roomOccupancies', function (Builder $query) use ($checkInOn, $checkOutOn): void {
                $query->whereNull('checked_out_at');

                if ($checkInOn->isAfter(today())) {
                    $query
                        ->whereDate('checked_in_at', '<', $checkOutOn->toDateString())
                        ->whereDate('expected_check_out_on', '>', $checkInOn->toDateString());
                }
            })
            ->whereDoesntHave('reservedRooms', function (Builder $query) use ($checkInOn, $checkOutOn, $exceptReservation): void {
                $query
                    ->whereDate('planned_check_in_on', '<', $checkOutOn->toDateString())
                    ->whereDate('planned_check_out_on', '>', $checkInOn->toDateString())
                    ->whereHas('reservation', function (Builder $reservationQuery) use ($exceptReservation): void {
                        $reservationQuery->where('status', ReservationStatus::Confirmed);

                        if ($exceptReservation !== null) {
                            $reservationQuery->whereKeyNot($exceptReservation->id);
                        }
                    });
            });
    }

    public function hasConfirmedReservationConflict(Room $room, CarbonImmutable $checkInOn, CarbonImmutable $checkOutOn, null|Reservation $exceptReservation = null): bool
    {
        return $room->reservedRooms()
            ->whereDate('planned_check_in_on', '<', $checkOutOn->toDateString())
            ->whereDate('planned_check_out_on', '>', $checkInOn->toDateString())
            ->whereHas('reservation', function (Builder $query) use ($exceptReservation): void {
                $query->where('status', ReservationStatus::Confirmed);

                if ($exceptReservation !== null) {
                    $query->whereKeyNot($exceptReservation->id);
                }
            })
            ->exists();
    }
}
