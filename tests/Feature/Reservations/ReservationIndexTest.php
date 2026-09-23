<?php

declare(strict_types=1);

namespace Tests\Feature\Reservations;

use App\Constants\ReservationStatus;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\OperationalTestCase;

class ReservationIndexTest extends OperationalTestCase
{
    use InteractsWithReservations;
    use RefreshDatabase;

    #[Test]
    public function it_lists_only_the_selected_hotels_reservations_and_filters_by_status(): void
    {
        [$reservation] = $this->reservationWithDetails(ReservationStatus::Confirmed);
        Reservation::factory()->create();

        $this->get(route('hotels.reservations.index', [
            $reservation->hotel,
            'status' => ReservationStatus::Confirmed->value,
        ]))->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Hotels/Reservations/Index')
            ->has('reservations.data', 1)
            ->where('reservations.data.0.id', $reservation->id)
            ->where('filters.status', ReservationStatus::Confirmed->value));
    }
}
