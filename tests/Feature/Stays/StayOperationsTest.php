<?php

declare(strict_types=1);

namespace Tests\Feature\Stays;

use App\Constants\HousekeepingStatus;
use App\Constants\RoomOccupancyEndReason;
use App\Constants\StayGuestRole;
use App\Constants\StayStatus;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomOccupancy;
use App\Models\RoomType;
use App\Models\Stay;
use App\Models\StayGuest;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StayOperationsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_the_expected_check_out_for_an_active_stay(): void
    {
        [$hotel, $stay] = $this->activeStay();
        $expectedCheckOut = now()->addDays(3)->toDateString();

        $this->patch(route('hotels.stays.expected-check-out.update', [$hotel, $stay]), [
            'expected_check_out_on' => $expectedCheckOut,
        ])->assertSessionHasNoErrors();

        $this->assertSame($expectedCheckOut, $stay->refresh()->expected_check_out_on->toDateString());
        $this->assertSame($expectedCheckOut, $stay->roomOccupancies()->sole()->expected_check_out_on->toDateString());
    }

    #[Test]
    public function it_checks_out_a_stay_and_sends_the_room_to_housekeeping(): void
    {
        [$hotel, $stay, $occupancy] = $this->activeStay();

        $this->post(route('hotels.stays.check-out', [$hotel, $stay]))
            ->assertSessionHasNoErrors();

        $this->assertSame(StayStatus::CheckedOut, $stay->refresh()->status);
        $this->assertNotNull($occupancy->refresh()->checked_out_at);
        $this->assertSame(RoomOccupancyEndReason::CheckOut, $occupancy->end_reason);
        $this->assertSame(HousekeepingStatus::Dirty, $occupancy->room->refresh()->housekeeping_status);
    }

    #[Test]
    public function it_transfers_an_active_room_occupancy_without_fragmenting_the_stay(): void
    {
        [$hotel, $stay, $occupancy] = $this->activeStay();
        $newRoom = Room::factory()->for($hotel)->for($occupancy->room->roomType)->create(['number' => '202']);

        $this->post(route('hotels.stays.room-occupancies.transfer', [$hotel, $stay, $occupancy]), [
            'room_id' => $newRoom->id,
            'nightly_rate' => '175000.00',
        ])->assertSessionHasNoErrors();

        $this->assertNotNull($occupancy->refresh()->checked_out_at);
        $this->assertSame(RoomOccupancyEndReason::Transfer, $occupancy->end_reason);
        $this->assertSame(HousekeepingStatus::Dirty, $occupancy->room->refresh()->housekeeping_status);
        $newOccupancy = $stay->roomOccupancies()->latest('id')->firstOrFail();

        $this->assertSame($stay->id, $newOccupancy->stay_id);
        $this->assertSame($newRoom->id, $newOccupancy->room_id);
        $this->assertSame(1, $occupancy->guests()->count());
        $this->assertSame(1, $newOccupancy->guests()->count());
        $this->assertSame(
            $occupancy->guests()->sole()->id,
            $newOccupancy->guests()->sole()->id,
        );
    }

    #[Test]
    public function it_enforces_one_guest_assignment_per_room_occupancy(): void
    {
        [, , $occupancy] = $this->activeStay();

        $guestId = $occupancy->guests()->sole()->id;

        $this->expectException(QueryException::class);

        $occupancy->guests()->attach($guestId);
    }

    #[Test]
    public function it_prevents_a_second_check_out(): void
    {
        [$hotel, $stay] = $this->activeStay();

        $this->post(route('hotels.stays.check-out', [$hotel, $stay]))->assertSessionHasNoErrors();
        $this->post(route('hotels.stays.check-out', [$hotel, $stay]))
            ->assertSessionHasErrors('checked_out_at');
    }

    #[Test]
    public function it_summarizes_final_lodging_costs_for_a_checked_out_stay(): void
    {
        [$hotel, $stay, $occupancy] = $this->activeStay();
        $stay->update([
            'status' => StayStatus::CheckedOut,
            'checked_in_at' => '2026-08-25 14:00:00',
            'expected_check_out_on' => '2026-08-27',
            'checked_out_at' => '2026-08-27 10:00:00',
        ]);
        $occupancy->update([
            'nightly_rate' => '65000.00',
            'checked_in_at' => '2026-08-25 14:00:00',
            'expected_check_out_on' => '2026-08-27',
            'checked_out_at' => '2026-08-27 10:00:00',
            'end_reason' => RoomOccupancyEndReason::CheckOut,
        ]);

        $this->get(route('hotels.stays.show', [$hotel, $stay]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Hotels/Stays/Show')
                ->where('stayCostSummary.is_estimate', false)
                ->where('stayCostSummary.total_nights', 2)
                ->where('stayCostSummary.total_amount', '130000.00')
                ->where('stayCostSummary.items.0.room_occupancy_id', $occupancy->id)
                ->where('stayCostSummary.items.0.billable_nights', 2)
                ->where('stayCostSummary.items.0.nightly_rate', '65000.00')
                ->where('stayCostSummary.items.0.subtotal_amount', '130000.00')
                ->where('stayCostSummary.items.0.period_start_on', '2026-08-25')
                ->where('stayCostSummary.items.0.period_end_on', '2026-08-27'));
    }

    #[Test]
    public function it_charges_at_least_one_night_for_same_day_checkout(): void
    {
        [$hotel, $stay, $occupancy] = $this->activeStay();
        $stay->update([
            'status' => StayStatus::CheckedOut,
            'checked_in_at' => '2026-08-25 09:00:00',
            'expected_check_out_on' => '2026-08-25',
            'checked_out_at' => '2026-08-25 18:00:00',
        ]);
        $occupancy->update([
            'nightly_rate' => '65000.00',
            'checked_in_at' => '2026-08-25 09:00:00',
            'expected_check_out_on' => '2026-08-25',
            'checked_out_at' => '2026-08-25 18:00:00',
            'end_reason' => RoomOccupancyEndReason::CheckOut,
        ]);

        $this->get(route('hotels.stays.show', [$hotel, $stay]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('stayCostSummary.is_estimate', false)
                ->where('stayCostSummary.total_nights', 1)
                ->where('stayCostSummary.total_amount', '65000.00')
                ->where('stayCostSummary.items.0.billable_nights', 1));
    }

    #[Test]
    public function it_summarizes_active_stay_costs_as_an_estimate(): void
    {
        [$hotel, $stay, $occupancy] = $this->activeStay();
        $stay->update([
            'checked_in_at' => '2026-08-25 14:00:00',
            'expected_check_out_on' => '2026-08-28',
        ]);
        $occupancy->update([
            'nightly_rate' => '65000.00',
            'checked_in_at' => '2026-08-25 14:00:00',
            'expected_check_out_on' => '2026-08-28',
        ]);

        $this->get(route('hotels.stays.show', [$hotel, $stay]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('stayCostSummary.is_estimate', true)
                ->where('stayCostSummary.total_nights', 3)
                ->where('stayCostSummary.total_amount', '195000.00')
                ->where('stayCostSummary.items.0.period_end_on', '2026-08-28'));
    }

    #[Test]
    public function it_sums_lodging_costs_across_transferred_room_occupancies(): void
    {
        [$hotel, $stay, $firstOccupancy] = $this->activeStay();
        $secondRoom = Room::factory()->for($hotel)->for($firstOccupancy->room->roomType)->create(['number' => '202']);
        $stay->update([
            'status' => StayStatus::CheckedOut,
            'checked_in_at' => '2026-08-25 14:00:00',
            'expected_check_out_on' => '2026-08-28',
            'checked_out_at' => '2026-08-28 10:00:00',
        ]);
        $firstOccupancy->update([
            'nightly_rate' => '65000.00',
            'checked_in_at' => '2026-08-25 14:00:00',
            'expected_check_out_on' => '2026-08-28',
            'checked_out_at' => '2026-08-26 11:00:00',
            'end_reason' => RoomOccupancyEndReason::Transfer,
        ]);
        $secondOccupancy = RoomOccupancy::factory()->for($stay)->for($secondRoom)->create([
            'nightly_rate' => '80000.00',
            'checked_in_at' => '2026-08-26 11:00:00',
            'expected_check_out_on' => '2026-08-28',
            'checked_out_at' => '2026-08-28 10:00:00',
            'end_reason' => RoomOccupancyEndReason::CheckOut,
        ]);

        $this->get(route('hotels.stays.show', [$hotel, $stay]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('stayCostSummary.is_estimate', false)
                ->where('stayCostSummary.total_nights', 3)
                ->where('stayCostSummary.total_amount', '225000.00')
                ->where('stayCostSummary.items.0.room_occupancy_id', $firstOccupancy->id)
                ->where('stayCostSummary.items.0.subtotal_amount', '65000.00')
                ->where('stayCostSummary.items.1.room_occupancy_id', $secondOccupancy->id)
                ->where('stayCostSummary.items.1.subtotal_amount', '160000.00'));
    }

    /** @return array{Hotel, Stay, RoomOccupancy} */
    private function activeStay(): array
    {
        $hotel = Hotel::factory()->create();
        $roomType = RoomType::factory()->for($hotel)->create(['capacity' => 2]);
        $room = Room::factory()->for($hotel)->for($roomType)->create(['number' => '201']);
        $guest = Guest::factory()->for($hotel)->create();
        $stay = Stay::factory()->for($hotel)->create(['responsible_guest_id' => $guest->id]);
        $stayGuest = StayGuest::factory()->for($stay)->for($guest)->create(['role' => StayGuestRole::Responsible]);
        $occupancy = RoomOccupancy::factory()->for($stay)->for($room)->create();
        $occupancy->guests()->attach($stayGuest->guest_id);

        return [$hotel, $stay, $occupancy];
    }
}
