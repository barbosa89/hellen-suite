<?php

declare(strict_types=1);

namespace Tests\Feature\Hotels;

use App\Constants\FolioAdjustmentDirection;
use App\Constants\HousekeepingStatus;
use App\Constants\PaymentType;
use App\Models\FolioAdjustment;
use App\Models\FolioCharge;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\ReservedRoom;
use App\Models\Room;
use App\Models\RoomOccupancy;
use App\Models\Stay;
use App\Models\StayFolio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HotelManagementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_an_empty_dashboard_without_dividing_by_zero(): void
    {
        $this->travelTo('2026-10-01 10:00:00');
        $hotel = Hotel::factory()->create();

        $this->get(route('hotels.management.index', $hotel))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Hotels/Management/Index')
                ->where('hotel.id', $hotel->getKey())
                ->where('dashboard.asOf', '2026-10-01')
                ->where('dashboard.currency', 'COP')
                ->where('dashboard.metrics.activeRooms', 0)
                ->where('dashboard.metrics.occupiedRooms', 0)
                ->where('dashboard.metrics.occupancyRate', 0)
                ->where('dashboard.metrics.adr', 0)
                ->where('dashboard.metrics.revPar', 0)
                ->where('dashboard.metrics.outstandingBalanceMinor', 0)
                ->has('dashboard.forecast', 7)
                ->where('dashboard.forecast.0.occupancyRate', 0));
    }

    #[Test]
    public function it_reports_current_operational_and_financial_metrics_for_only_the_selected_hotel(): void
    {
        $this->travelTo('2026-10-01 10:00:00');

        $hotel = Hotel::factory()->create();
        $otherHotel = Hotel::factory()->create();
        $rooms = Room::factory()->for($hotel)->count(4)->create();
        Room::factory()->for($hotel)->create(['is_active' => false]);
        Room::factory()->for($otherHotel)->create();
        $rooms[2]->update(['housekeeping_status' => HousekeepingStatus::Dirty]);

        $firstStay = Stay::factory()->for($hotel)->create([
            'expected_check_out_on' => today(),
        ]);
        $secondStay = Stay::factory()->for($hotel)->create([
            'expected_check_out_on' => today()->addDays(2),
        ]);
        $firstOccupancy = RoomOccupancy::factory()->for($firstStay)->for($rooms[0])->create([
            'nightly_rate' => 100_000,
            'checked_in_at' => today()->subDay()->addHours(15),
            'expected_check_out_on' => today(),
        ]);
        $secondOccupancy = RoomOccupancy::factory()->for($secondStay)->for($rooms[1])->create([
            'nightly_rate' => 200_000,
            'checked_in_at' => today()->subDay()->addHours(15),
            'expected_check_out_on' => today()->addDays(2),
        ]);

        $firstGuest = Guest::factory()->for($hotel)->create();
        $secondGuest = Guest::factory()->for($hotel)->create();
        $firstOccupancy->guests()->attach([$firstGuest->id, $secondGuest->id]);
        $secondOccupancy->guests()->attach($firstGuest->id);

        Reservation::factory()->for($hotel)->confirmed()->create([
            'planned_check_in_on' => today(),
            'planned_check_out_on' => today()->addDay(),
        ]);
        Reservation::factory()->for($hotel)->create([
            'planned_check_in_on' => today(),
            'planned_check_out_on' => today()->addDay(),
        ]);
        Reservation::factory()->for($otherHotel)->confirmed()->create([
            'planned_check_in_on' => today(),
            'planned_check_out_on' => today()->addDay(),
        ]);

        $folio = StayFolio::factory()->for($hotel)->for($firstStay)->create();
        FolioCharge::factory()->for($folio, 'folio')->create(['total_amount_minor' => 100_000]);
        FolioAdjustment::factory()->for($folio, 'folio')->create([
            'direction' => FolioAdjustmentDirection::Debit,
            'amount_minor' => 20_000,
        ]);
        FolioAdjustment::factory()->for($folio, 'folio')->create([
            'direction' => FolioAdjustmentDirection::Credit,
            'amount_minor' => 10_000,
        ]);
        $receipt = Payment::factory()->for($folio, 'folio')->create(['amount_minor' => 30_000]);
        Payment::factory()->for($folio, 'folio')->create([
            'type' => PaymentType::Refund,
            'amount_minor' => 5_000,
            'parent_payment_id' => $receipt,
        ]);

        $otherStay = Stay::factory()->for($otherHotel)->create();
        $otherFolio = StayFolio::factory()->for($otherHotel)->for($otherStay)->create();
        FolioCharge::factory()->for($otherFolio, 'folio')->create(['total_amount_minor' => 900_000]);

        $this->get(route('hotels.management.index', $hotel))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('dashboard.metrics.activeRooms', 4)
                ->where('dashboard.metrics.occupiedRooms', 2)
                ->where('dashboard.metrics.availableRooms', 2)
                ->where('dashboard.metrics.dirtyRooms', 1)
                ->where('dashboard.metrics.occupancyRate', 50)
                ->where('dashboard.metrics.adr', 150_000)
                ->where('dashboard.metrics.revPar', 75_000)
                ->where('dashboard.metrics.arrivalsToday', 1)
                ->where('dashboard.metrics.departuresToday', 1)
                ->where('dashboard.metrics.guestsInHouse', 2)
                ->where('dashboard.metrics.outstandingBalanceMinor', 85_000));
    }

    #[Test]
    public function it_forecasts_active_stays_and_confirmed_reservations_with_exclusive_departure_dates(): void
    {
        $this->travelTo('2026-10-01 10:00:00');

        $hotel = Hotel::factory()->create();
        $rooms = Room::factory()->for($hotel)->count(3)->create();
        $stay = Stay::factory()->for($hotel)->create();
        RoomOccupancy::factory()->for($stay)->for($rooms[0])->create([
            'checked_in_at' => today()->subDay()->addHours(15),
            'expected_check_out_on' => today()->addDays(2),
        ]);

        $confirmed = Reservation::factory()->for($hotel)->confirmed()->create([
            'planned_check_in_on' => today()->addDay(),
            'planned_check_out_on' => today()->addDays(3),
        ]);
        ReservedRoom::factory()->for($confirmed)->for($rooms[1])->create();

        $draft = Reservation::factory()->for($hotel)->create([
            'planned_check_in_on' => today(),
            'planned_check_out_on' => today()->addDays(5),
        ]);
        ReservedRoom::factory()->for($draft)->for($rooms[2])->create();

        $this->get(route('hotels.management.index', $hotel))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('dashboard.forecast.0.date', '2026-10-01')
                ->where('dashboard.forecast.0.occupiedRooms', 1)
                ->where('dashboard.forecast.0.occupancyRate', 33.3)
                ->where('dashboard.forecast.1.occupiedRooms', 2)
                ->where('dashboard.forecast.1.occupancyRate', 66.7)
                ->where('dashboard.forecast.2.occupiedRooms', 1)
                ->where('dashboard.forecast.2.occupancyRate', 33.3)
                ->where('dashboard.forecast.3.occupiedRooms', 0)
                ->where('dashboard.forecast.6.date', '2026-10-07'));
    }
}
