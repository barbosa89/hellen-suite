<?php

declare(strict_types=1);

namespace Tests\Feature\Reservations;

use App\Constants\ReservationEventType;
use App\Constants\ReservationStatus;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\IdentificationType;
use App\Models\Reservation;
use App\Settings\GeneralSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ReservationStoreTest extends TestCase
{
    use InteractsWithReservations;
    use RefreshDatabase;

    #[Test]
    public function it_creates_a_draft_with_guests_rooms_rates_assignments_and_history(): void
    {
        GeneralSettings::fake(['currency' => 'COP']);
        $hotel = Hotel::factory()->create();
        $identificationType = IdentificationType::factory()->create();
        $guest = Guest::factory()->for($hotel)->create(['identification_type_id' => $identificationType->id]);
        $room = $this->room($hotel);

        $this->post(route('hotels.reservations.store', $hotel), $this->reservationPayload($guest, $room))
            ->assertRedirect();

        $reservation = Reservation::query()->with(['reservationGuests.reservedRooms', 'reservedRooms', 'events'])->sole();

        $this->assertSame(ReservationStatus::Draft, $reservation->status);
        $this->assertSame('150000.00', $reservation->reservedRooms->sole()->nightly_rate);
        $this->assertSame($guest->id, $reservation->reservationGuests->sole()->guest_id);
        $this->assertTrue($reservation->reservationGuests->sole()->reservedRooms->sole()->is($reservation->reservedRooms->sole()));
        $this->assertSame(ReservationEventType::Created, $reservation->events->sole()->type);
    }

    #[Test]
    public function it_rejects_cross_hotel_records_and_duplicate_assignments(): void
    {
        GeneralSettings::fake(['currency' => 'COP']);

        $hotel = Hotel::factory()->create();
        $otherHotel = Hotel::factory()->create();
        $foreignGuest = Guest::factory()->for($otherHotel)->create();
        $room = $this->room($hotel);

        $this->post(route('hotels.reservations.store', $hotel), $this->reservationPayload($foreignGuest, $room))
            ->assertSessionHasErrors('guests.0.guest_id');
    }
}
