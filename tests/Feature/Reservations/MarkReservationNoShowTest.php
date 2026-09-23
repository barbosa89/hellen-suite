<?php

declare(strict_types=1);

namespace Tests\Feature\Reservations;

use App\Constants\ReservationStatus;
use App\Settings\GeneralSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MarkReservationNoShowTest extends TestCase
{
    use InteractsWithReservations;
    use RefreshDatabase;

    #[Test]
    public function it_marks_a_due_confirmed_reservation_as_no_show(): void
    {
        GeneralSettings::fake(['currency' => 'COP']);
        [$reservation] = $this->reservationWithDetails(
            ReservationStatus::Confirmed,
            today()->toDateString(),
            today()->addDay()->toDateString(),
        );

        $this->patch(route('hotels.reservations.no-show', [$reservation->hotel, $reservation]))
            ->assertSessionHasNoErrors();

        $this->assertSame(ReservationStatus::NoShow, $reservation->refresh()->status);
    }

    #[Test]
    public function it_does_not_mark_a_future_arrival_as_no_show(): void
    {
        GeneralSettings::fake(['currency' => 'COP']);
        [$reservation] = $this->reservationWithDetails(ReservationStatus::Confirmed);

        $this->patch(route('hotels.reservations.no-show', [$reservation->hotel, $reservation]))
            ->assertSessionHasErrors('reservation');
    }
}
