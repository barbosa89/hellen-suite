<?php

declare(strict_types=1);

namespace Tests\Feature\Reservations;

use App\Constants\ReservationStatus;
use App\Models\Guest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\OperationalTestCase;

class ConfirmReservationTest extends OperationalTestCase
{
    use InteractsWithReservations;
    use RefreshDatabase;

    #[Test]
    public function it_confirms_a_draft_and_blocks_an_overlapping_reservation(): void
    {
        [$reservation, $room] = $this->reservationWithDetails();

        $this->patch(route('hotels.reservations.confirm', [$reservation->hotel, $reservation]))
            ->assertSessionHasNoErrors();

        $this->assertSame(ReservationStatus::Confirmed, $reservation->refresh()->status);
        $otherGuest = Guest::factory()->for($reservation->hotel)->create();

        $this->post(
            route('hotels.reservations.store', $reservation->hotel),
            $this->reservationPayload($otherGuest, $room),
        )->assertSessionHasErrors('reserved_rooms');
    }

    #[Test]
    public function it_allows_adjacent_confirmed_reservations(): void
    {
        [$firstReservation, $room] = $this->reservationWithDetails(
            ReservationStatus::Confirmed,
            now()->addDay()->toDateString(),
            now()->addDays(2)->toDateString(),
        );
        $guest = Guest::factory()->for($firstReservation->hotel)->create();
        $this->post(route('hotels.reservations.store', $firstReservation->hotel), $this->reservationPayload(
            $guest,
            $room,
            now()->addDays(2)->toDateString(),
            now()->addDays(3)->toDateString(),
        ))->assertSessionHasNoErrors();
    }
}
