<?php

declare(strict_types=1);

namespace Tests\Feature\Rooms;

use App\Constants\HousekeepingStatus;
use App\Models\Hotel;
use App\Models\Reservation;
use App\Models\ReservedRoom;
use App\Models\Room;
use App\Models\RoomOccupancy;
use App\Models\RoomType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RoomIndexTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_rooms_for_a_hotel(): void
    {
        $hotel = Hotel::factory()->create();

        $this->get(route('hotels.rooms.index', $hotel))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Hotels/Rooms/Index')
                ->where('hotel.id', $hotel->getKey()));
    }

    #[Test]
    public function it_displays_only_confirmed_room_reservations(): void
    {
        $this->travelTo(now()->startOfDay()->addHours(10));

        $confirmedCheckInOn = today()->addDays(11)->toDateString();
        $confirmedCheckOutOn = today()->addDays(14)->toDateString();
        $laterConfirmedCheckInOn = today()->addDays(19)->toDateString();
        $laterConfirmedCheckOutOn = today()->addDays(21)->toDateString();

        $hotel = Hotel::factory()->create();
        $room = Room::factory()->for($hotel)->create(['number' => '101']);
        $confirmedReservation = Reservation::factory()->for($hotel)->confirmed()->create([
            'planned_check_in_on' => $confirmedCheckInOn,
            'planned_check_out_on' => $confirmedCheckOutOn,
        ]);
        $laterConfirmedReservation = Reservation::factory()->for($hotel)->confirmed()->create([
            'planned_check_in_on' => $laterConfirmedCheckInOn,
            'planned_check_out_on' => $laterConfirmedCheckOutOn,
        ]);
        $draftReservation = Reservation::factory()->for($hotel)->create();

        ReservedRoom::factory()->for($confirmedReservation)->for($room)->create([
            'planned_check_in_on' => $confirmedCheckInOn,
            'planned_check_out_on' => $confirmedCheckOutOn,
        ]);
        ReservedRoom::factory()->for($laterConfirmedReservation)->for($room)->create([
            'planned_check_in_on' => $laterConfirmedCheckInOn,
            'planned_check_out_on' => $laterConfirmedCheckOutOn,
        ]);
        ReservedRoom::factory()->for($draftReservation)->for($room)->create();

        $this->get(route('hotels.rooms.index', $hotel))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('rooms.data', 1)
                ->has('rooms.data.0.reserved_rooms', 2)
                ->where('rooms.data.0.reserved_rooms.0.reservation_id', $confirmedReservation->getKey())
                ->where('rooms.data.0.reserved_rooms.0.planned_check_in_on', $confirmedCheckInOn)
                ->where('rooms.data.0.reserved_rooms.0.planned_check_out_on', $confirmedCheckOutOn)
                ->where('rooms.data.0.reserved_rooms.1.reservation_id', $laterConfirmedReservation->getKey()));
    }

    #[Test]
    public function it_reports_room_availability_for_the_selected_period(): void
    {
        $this->travelTo(now()->startOfDay()->addHours(10));

        $checkInOn = today()->toDateString();
        $checkOutOn = today()->addDay()->toDateString();
        $reservationCheckOutOn = today()->addDays(3)->toDateString();
        $occupancyCheckedInAt = today()->subDays(2)->setTime(15, 0)->toDateTimeString();
        $occupancyExpectedCheckOutOn = today()->addDays(2)->toDateString();

        $hotel = Hotel::factory()->create();
        $availableRoom = Room::factory()->for($hotel)->create(['number' => '101']);
        $reservedRoom = Room::factory()->for($hotel)->create(['number' => '102']);
        $occupiedRoom = Room::factory()->for($hotel)->create(['number' => '103']);
        $reservation = Reservation::factory()->for($hotel)->confirmed()->create([
            'planned_check_in_on' => $checkInOn,
            'planned_check_out_on' => $reservationCheckOutOn,
        ]);

        ReservedRoom::factory()->for($reservation)->for($reservedRoom)->create([
            'planned_check_in_on' => $checkInOn,
            'planned_check_out_on' => $reservationCheckOutOn,
        ]);
        RoomOccupancy::factory()->for($occupiedRoom)->create([
            'checked_in_at' => $occupancyCheckedInAt,
            'expected_check_out_on' => $occupancyExpectedCheckOutOn,
        ]);

        $this->get(route('hotels.rooms.index', [
            'hotel' => $hotel,
            'check_in_on' => $checkInOn,
            'check_out_on' => $checkOutOn,
        ]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('availabilityPeriod.check_in_on', $checkInOn)
                ->where('availabilityPeriod.check_out_on', $checkOutOn)
                ->where('availabilityPeriod.today', $checkInOn)
                ->where('availabilitySummary.available', 1)
                ->where('availabilitySummary.total', 3)
                ->where('rooms.data.0.id', $availableRoom->id)
                ->where('rooms.data.0.is_available', true)
                ->where('rooms.data.1.id', $reservedRoom->id)
                ->where('rooms.data.1.is_available', false)
                ->where('rooms.data.2.id', $occupiedRoom->id)
                ->where('rooms.data.2.is_available', false)
                ->has('rooms.data.2.room_occupancies', 1));
    }

    #[Test]
    public function it_rejects_an_invalid_availability_period(): void
    {
        $this->travelTo(now()->startOfDay()->addHours(10));

        $today = today()->toDateString();
        $hotel = Hotel::factory()->create();

        $this->get(route('hotels.rooms.index', [
            'hotel' => $hotel,
            'check_in_on' => $today,
            'check_out_on' => $today,
        ]))->assertSessionHasErrors('check_out_on');
    }

    #[Test]
    public function it_returns_not_found_for_an_unknown_hotel(): void
    {
        $this->get(route('hotels.rooms.index', 999_999))->assertNotFound();
    }

    #[Test]
    public function rooms_belong_to_a_hotel_and_room_type(): void
    {
        $hotel = Hotel::factory()->create();
        $room = Room::factory()->for($hotel)->create();

        $this->assertTrue($room->hotel->is($hotel));
        $this->assertTrue($hotel->rooms->contains($room));
        $this->assertTrue($room->roomType->hotel->is($hotel));
        $this->assertTrue($room->roomType->rooms->contains($room));
    }

    #[Test]
    public function room_types_belong_to_a_hotel(): void
    {
        $hotel = Hotel::factory()->create();
        $roomType = RoomType::factory()->for($hotel)->create();

        $this->assertTrue($roomType->hotel->is($hotel));
        $this->assertTrue($hotel->roomTypes->contains($roomType));
    }

    #[Test]
    public function rooms_use_operational_defaults(): void
    {
        $room = Room::factory()->create();

        $this->assertSame(HousekeepingStatus::Clean, $room->housekeeping_status);
        $this->assertTrue($room->is_active);
    }
}
