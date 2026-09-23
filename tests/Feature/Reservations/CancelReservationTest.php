<?php

declare(strict_types=1);

namespace Tests\Feature\Reservations;

use App\Constants\ReservationStatus;
use App\Models\Guest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\OperationalTestCase;

class CancelReservationTest extends OperationalTestCase
{
    use InteractsWithReservations;
    use RefreshDatabase;

    #[Test]
    public function it_cancels_a_confirmed_reservation_and_releases_the_room(): void
    {
        [$reservation, $room] = $this->reservationWithDetails(ReservationStatus::Confirmed);

        $this->patch(route('hotels.reservations.cancel', [$reservation->hotel, $reservation]))
            ->assertSessionHasNoErrors();

        $this->assertSame(ReservationStatus::Cancelled, $reservation->refresh()->status);

        $guest = Guest::factory()->for($reservation->hotel)->create();

        $this->post(
            route('hotels.reservations.store', $reservation->hotel),
            $this->reservationPayload($guest, $room),
        )->assertSessionHasNoErrors();
    }
}
