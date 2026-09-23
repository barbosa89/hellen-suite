<?php

declare(strict_types=1);

namespace App\Actions\Reservations;

use App\Models\Reservation;

final class ReservationSnapshot
{
    /** @return array<string, mixed> */
    public function execute(Reservation $reservation): array
    {
        $reservation->loadMissing([
            'reservationGuests.guest:id,first_name,last_name,identification_number',
            'reservedRooms.room:id,number,room_type_id',
            'reservedRooms.reservationGuests',
        ]);

        return [
            'status' => $reservation->status->value,
            'planned_check_in_on' => $reservation->planned_check_in_on->toDateString(),
            'planned_check_out_on' => $reservation->planned_check_out_on->toDateString(),
            'responsible_guest_id' => $reservation->responsible_guest_id,
            'guests' => $reservation->reservationGuests->map(fn ($reservationGuest): array => [
                'guest_id' => $reservationGuest->guest_id,
                'role' => $reservationGuest->role->value,
            ])->values()->all(),
            'rooms' => $reservation->reservedRooms->map(fn ($reservedRoom): array => [
                'room_id' => $reservedRoom->room_id,
                'nightly_rate' => $reservedRoom->nightly_rate,
                'guest_ids' => $reservedRoom->reservationGuests
                    ->map(fn ($reservationGuest): int => $reservationGuest->guest_id)
                    ->values()
                    ->all(),
            ])->values()->all(),
        ];
    }
}
