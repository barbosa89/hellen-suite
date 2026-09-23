<?php

declare(strict_types=1);

namespace Tests\Feature\Reservations;

use App\Constants\ReservationStatus;
use App\Constants\StayGuestRole;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\IdentificationType;
use App\Models\Reservation;
use App\Models\ReservationGuest;
use App\Models\ReservedRoom;
use App\Models\Room;
use App\Models\RoomType;

trait InteractsWithReservations
{
    protected function room(Hotel $hotel, string $number = '101', int $capacity = 2): Room
    {
        $roomType = RoomType::factory()->for($hotel)->create(['capacity' => $capacity]);

        return Room::factory()->for($hotel)->for($roomType)->create(['number' => $number]);
    }

    /** @return array<string, mixed> */
    protected function reservationPayload(Guest $guest, Room $room, null|string $checkInOn = null, null|string $checkOutOn = null): array
    {
        return [
            'planned_check_in_on' => $checkInOn ?? now()->addDay()->toDateString(),
            'planned_check_out_on' => $checkOutOn ?? now()->addDays(2)->toDateString(),
            'responsible_guest_key' => 'responsible',
            'guests' => [['key' => 'responsible', 'guest_id' => $guest->id]],
            'reserved_rooms' => [[
                'room_id' => $room->id,
                'nightly_rate' => '150000.00',
                'guest_keys' => ['responsible'],
            ]],
        ];
    }

    /** @return array{Reservation, Room, Guest} */
    protected function reservationWithDetails(
        ReservationStatus $status = ReservationStatus::Draft,
        null|string $checkInOn = null,
        null|string $checkOutOn = null,
    ): array {
        $hotel = Hotel::factory()->create();
        $identificationType = IdentificationType::factory()->create();
        $guest = Guest::factory()->for($hotel)->create(['identification_type_id' => $identificationType->id]);
        $room = $this->room($hotel);
        $reservation = Reservation::factory()->for($hotel)->create([
            'responsible_guest_id' => $guest->id,
            'status' => $status,
            'planned_check_in_on' => $checkInOn ?? now()->addDay()->toDateString(),
            'planned_check_out_on' => $checkOutOn ?? now()->addDays(2)->toDateString(),
            'confirmed_at' => $status === ReservationStatus::Confirmed ? now() : null,
        ]);
        $reservationGuest = ReservationGuest::factory()->for($reservation)->for($guest)->create([
            'role' => StayGuestRole::Responsible,
        ]);
        $reservedRoom = ReservedRoom::factory()->for($reservation)->for($room)->create([
            'nightly_rate' => '150000.00',
            'planned_check_in_on' => $reservation->planned_check_in_on,
            'planned_check_out_on' => $reservation->planned_check_out_on,
        ]);
        $reservedRoom->reservationGuests()->attach($reservationGuest);

        return [$reservation, $room, $guest];
    }
}
