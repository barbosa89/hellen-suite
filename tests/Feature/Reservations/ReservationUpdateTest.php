<?php

declare(strict_types=1);

namespace Tests\Feature\Reservations;

use App\Constants\ReservationEventType;
use App\Constants\ReservationStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\OperationalTestCase;

class ReservationUpdateTest extends OperationalTestCase
{
    use InteractsWithReservations;
    use RefreshDatabase;

    #[Test]
    public function it_updates_a_confirmed_reservation_without_conflicting_with_itself(): void
    {
        [$reservation, $room, $guest] = $this->reservationWithDetails(ReservationStatus::Confirmed);

        $payload = $this->reservationPayload($guest, $room);
        $payload['reserved_rooms'][0]['nightly_rate'] = '175000.00';

        $this->patch(route('hotels.reservations.update', [$reservation->hotel, $reservation]), $payload)
            ->assertRedirect(route('hotels.reservations.show', [$reservation->hotel, $reservation]));

        $this->assertSame('175000.00', $reservation->reservedRooms()->sole()->nightly_rate);
        $this->assertSame(ReservationEventType::Updated, $reservation->events()->latest()->firstOrFail()->type);
    }

    #[Test]
    public function it_rejects_updates_to_terminal_reservations(): void
    {
        [$reservation, $room, $guest] = $this->reservationWithDetails(ReservationStatus::Cancelled);

        $this->patch(
            route('hotels.reservations.update', [$reservation->hotel, $reservation]),
            $this->reservationPayload($guest, $room),
        )->assertSessionHasErrors('reservation');
    }
}
