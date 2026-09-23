<?php

declare(strict_types=1);

namespace Tests\Feature\Reservations;

use App\Constants\HousekeepingStatus;
use App\Constants\ReservationStatus;
use App\Constants\StayStatus;
use App\Models\Stay;
use App\Settings\GeneralSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CheckInReservationTest extends TestCase
{
    use InteractsWithReservations;
    use RefreshDatabase;

    #[Test]
    public function it_converts_a_due_reservation_to_one_linked_stay_with_the_agreed_terms(): void
    {
        GeneralSettings::fake(['currency' => 'COP']);

        [$reservation, $room, $guest] = $this->reservationWithDetails(
            ReservationStatus::Confirmed,
            today()->toDateString(),
            today()->addDay()->toDateString(),
        );

        $this->post(route('hotels.reservations.check-in', [$reservation->hotel, $reservation]))
            ->assertRedirect();

        $stay = Stay::query()->with(['stayGuests', 'roomOccupancies.guests'])->sole();

        $this->assertSame(StayStatus::Active, $stay->status);
        $this->assertSame($reservation->id, $stay->reservation_id);
        $this->assertSame($room->id, $stay->roomOccupancies->sole()->room_id);
        $this->assertSame('150000.00', $stay->roomOccupancies->sole()->nightly_rate);
        $this->assertSame($guest->id, $stay->roomOccupancies->sole()->guests->sole()->id);
        $this->assertSame(ReservationStatus::CheckedIn, $reservation->refresh()->status);
    }

    #[Test]
    public function it_rechecks_housekeeping_and_prevents_duplicate_check_in(): void
    {
        GeneralSettings::fake(['currency' => 'COP']);
        [$reservation, $room] = $this->reservationWithDetails(
            ReservationStatus::Confirmed,
            today()->toDateString(),
            today()->addDay()->toDateString(),
        );

        $room->update(['housekeeping_status' => HousekeepingStatus::Dirty]);

        $this->post(route('hotels.reservations.check-in', [$reservation->hotel, $reservation]))
            ->assertSessionHasErrors('reserved_rooms');
        $this->assertSame(0, Stay::query()->count());
    }
}
