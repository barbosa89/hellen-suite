<?php

declare(strict_types=1);

namespace Tests\Feature\Reservations;

use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\OperationalTestCase;

class ReservationShowTest extends OperationalTestCase
{
    use InteractsWithReservations;
    use RefreshDatabase;

    #[Test]
    public function it_shows_the_plan_quote_group_rooms_and_history(): void
    {
        [$reservation] = $this->reservationWithDetails();

        $this->get(route('hotels.reservations.show', [$reservation->hotel, $reservation]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Hotels/Reservations/Show')
                ->where('reservation.id', $reservation->id)
                ->has('reservation.reservation_guests', 1)
                ->has('reservation.reserved_rooms', 1)
                ->where('quote.nights', 1)
                ->where('quote.total', '150000.00'));
    }

    #[Test]
    public function scoped_binding_hides_another_hotels_reservation(): void
    {
        [$reservation] = $this->reservationWithDetails();
        $otherHotel = Hotel::factory()->create();

        $this->get(route('hotels.reservations.show', [$otherHotel, $reservation]))
            ->assertNotFound();
    }
}
