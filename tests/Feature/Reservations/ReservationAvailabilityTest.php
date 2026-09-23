<?php

declare(strict_types=1);

namespace Tests\Feature\Reservations;

use App\Constants\ReservationStatus;
use App\Models\Guest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\OperationalTestCase;

class ReservationAvailabilityTest extends OperationalTestCase
{
    use InteractsWithReservations;
    use RefreshDatabase;

    #[Test]
    public function confirmed_reservations_protect_walk_in_availability(): void
    {
        [$reservation, $room] = $this->reservationWithDetails(
            ReservationStatus::Confirmed,
            today()->toDateString(),
            today()->addDay()->toDateString(),
        );
        $walkInGuest = Guest::factory()->for($reservation->hotel)->create();

        $this->post(route('hotels.stays.store', $reservation->hotel), [
            'expected_check_out_on' => today()->addDay()->toDateString(),
            'responsible_guest_key' => 'responsible',
            'guests' => [['key' => 'responsible', 'guest_id' => $walkInGuest->id]],
            'room_occupancies' => [[
                'room_id' => $room->id,
                'nightly_rate' => '170000.00',
                'guest_keys' => ['responsible'],
            ]],
        ])->assertSessionHasErrors('room_occupancies.0.room_id');
    }
}
