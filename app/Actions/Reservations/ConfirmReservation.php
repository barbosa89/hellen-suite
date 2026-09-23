<?php

declare(strict_types=1);

namespace App\Actions\Reservations;

use App\Actions\Rooms\RoomAvailability;
use App\Constants\ReservationEventType;
use App\Constants\ReservationStatus;
use App\Models\Hotel;
use App\Models\Reservation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class ConfirmReservation
{
    public function __construct(
        private RoomAvailability $roomAvailability,
        private ReservationSnapshot $reservationSnapshot,
    ) {}

    public function execute(Hotel $hotel, Reservation $reservation): Reservation
    {
        return Cache::lock("hotels:{$hotel->id}:inventory", 10)->block(5, fn (): Reservation => DB::transaction(function () use ($hotel, $reservation): Reservation {
            $reservation = $hotel->reservations()
                ->with('reservedRooms')
                ->lockForUpdate()
                ->findOrFail($reservation->id);

            if ($reservation->status !== ReservationStatus::Draft || $reservation->planned_check_in_on->isBefore(today())) {
                throw ValidationException::withMessages([
                    'reservation' => trans('reservations.validation.cannot_confirm'),
                ]);
            }

            $availableRoomCount = $this->roomAvailability
                ->query($hotel, $reservation->planned_check_in_on->toImmutable(), $reservation->planned_check_out_on->toImmutable(), $reservation)
                ->whereKey($reservation->reservedRooms->pluck('room_id'))
                ->lockForUpdate()
                ->count();

            if ($availableRoomCount !== $reservation->reservedRooms->count()) {
                throw ValidationException::withMessages([
                    'reserved_rooms' => trans('reservations.validation.room_unavailable'),
                ]);
            }

            $before = $this->reservationSnapshot->execute($reservation);

            $reservation->update([
                'status' => ReservationStatus::Confirmed,
                'confirmed_at' => now(),
            ]);

            $reservation->events()->create([
                'type' => ReservationEventType::Confirmed,
                'before_data' => $before,
                'after_data' => $this->reservationSnapshot->execute($reservation->refresh()),
            ]);

            return $reservation;
        }));
    }
}
