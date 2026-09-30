<?php

declare(strict_types=1);

namespace Tests\Feature\Stays;

use App\Actions\Stays\CalculateOccupancyCharge;
use App\Actions\Stays\CreateStayFolio;
use App\Constants\FolioAdjustmentDirection;
use App\Constants\FolioAdjustmentType;
use App\Constants\HousekeepingStatus;
use App\Constants\LodgingChargePolicy;
use App\Constants\RoomOccupancyEndReason;
use App\Constants\RoomOccupancyEventType;
use App\Constants\StayGuestRole;
use App\Constants\StayStatus;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomOccupancy;
use App\Models\RoomType;
use App\Models\Stay;
use App\Models\StayGuest;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CheckOutRoomOccupancyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->travelTo(now()->startOfSecond());
    }

    #[Test]
    public function it_checks_out_one_room_while_the_stay_remains_active(): void
    {
        [$hotel, $stay, $firstOccupancy, $secondOccupancy] = $this->stayWithTwoRooms();
        $checkedOutAt = now()->subDay();
        $user = User::factory()->create();
        $this->settleOccupancy($firstOccupancy, $checkedOutAt);

        $this->actingAs($user);

        $this->post(route('hotels.stays.room-occupancies.check-out', [$hotel, $stay, $firstOccupancy]), [
            'checked_out_at' => $checkedOutAt->toDateTimeString(),
        ])->assertSessionHasNoErrors();

        $this->assertSame(StayStatus::Active, $stay->refresh()->status);
        $this->assertNull($stay->checked_out_at);
        $this->assertSame($checkedOutAt->toDateTimeString(), $firstOccupancy->refresh()->checked_out_at->toDateTimeString());
        $this->assertSame(RoomOccupancyEndReason::CheckOut, $firstOccupancy->end_reason);
        $this->assertSame(HousekeepingStatus::Dirty, $firstOccupancy->room->refresh()->housekeeping_status);
        $this->assertNull($secondOccupancy->refresh()->checked_out_at);
        $this->assertSame(HousekeepingStatus::Clean, $secondOccupancy->room->refresh()->housekeeping_status);

        $event = $firstOccupancy->events()->sole();
        $this->assertSame(RoomOccupancyEventType::CheckedOut, $event->type);
        $this->assertSame($user->id, $event->user_id);
        $this->assertNull($event->before_data['checked_out_at']);
        $this->assertSame($firstOccupancy->expected_check_out_on->toDateString(), $event->before_data['expected_check_out_on']);
        $this->assertSame($checkedOutAt->toDateTimeString(), $event->after_data['checked_out_at']);
        $this->assertSame(LodgingChargePolicy::ConsumedNights->value, $event->after_data['lodging_charge_policy']);
    }

    #[Test]
    public function it_finalizes_the_stay_when_the_last_active_room_checks_out(): void
    {
        [$hotel, $stay, $firstOccupancy, $secondOccupancy] = $this->stayWithTwoRooms();
        $firstOccupancy->update([
            'checked_out_at' => now()->subDay(),
            'end_reason' => RoomOccupancyEndReason::CheckOut,
        ]);
        $this->settleOccupancy($secondOccupancy, now());

        $this->post(route('hotels.stays.room-occupancies.check-out', [$hotel, $stay, $secondOccupancy]))
            ->assertSessionHasNoErrors();

        $this->assertSame(StayStatus::CheckedOut, $stay->refresh()->status);
        $this->assertSame(now()->toDateTimeString(), $stay->checked_out_at->toDateTimeString());
        $this->assertNotNull($secondOccupancy->refresh()->checked_out_at);
    }

    #[Test]
    public function it_rejects_a_check_out_before_the_room_check_in(): void
    {
        [$hotel, $stay, $firstOccupancy] = $this->stayWithTwoRooms();
        $this->settleOccupancy($firstOccupancy, now()->subDay());
        $checkedOutAt = $firstOccupancy->checked_in_at->copy()->subSecond();

        $this->post(route('hotels.stays.room-occupancies.check-out', [$hotel, $stay, $firstOccupancy]), [
            'checked_out_at' => $checkedOutAt->toDateTimeString(),
        ])->assertSessionHasErrors('checked_out_at');

        $this->assertNull($firstOccupancy->refresh()->checked_out_at);
    }

    #[Test]
    public function it_prevents_a_second_check_out_for_the_same_room(): void
    {
        [$hotel, $stay, $firstOccupancy] = $this->stayWithTwoRooms();
        $this->settleOccupancy($firstOccupancy, now()->subDay());

        $this->post(route('hotels.stays.room-occupancies.check-out', [$hotel, $stay, $firstOccupancy]), [
            'checked_out_at' => now()->subDay()->toDateTimeString(),
        ])->assertSessionHasNoErrors();

        $this->post(route('hotels.stays.room-occupancies.check-out', [$hotel, $stay, $firstOccupancy]))
            ->assertSessionHasErrors('checked_out_at');
    }

    #[Test]
    public function it_rejects_a_room_occupancy_from_another_stay(): void
    {
        [$hotel, $stay, $firstOccupancy] = $this->stayWithTwoRooms();
        $otherGuest = Guest::factory()->for($hotel)->create();
        $otherStay = Stay::factory()->for($hotel)->create(['responsible_guest_id' => $otherGuest->id]);
        $otherOccupancy = RoomOccupancy::factory()->for($otherStay)->for($firstOccupancy->room)->create();

        $this->post(route('hotels.stays.room-occupancies.check-out', [$hotel, $stay, $otherOccupancy]))
            ->assertNotFound();

        $this->assertNull($otherOccupancy->refresh()->checked_out_at);
    }

    #[Test]
    public function it_reports_final_and_projected_costs_after_a_partial_check_out(): void
    {
        [$hotel, $stay, $firstOccupancy] = $this->stayWithTwoRooms();
        $this->settleOccupancy($firstOccupancy, now()->subDay());

        $this->post(route('hotels.stays.room-occupancies.check-out', [$hotel, $stay, $firstOccupancy]), [
            'checked_out_at' => now()->subDay()->toDateTimeString(),
        ])->assertSessionHasNoErrors();

        $this->assertSame(1, $firstOccupancy->events()->count());

        $this->get(route('hotels.stays.show', [$hotel, $stay]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('stayCostSummary.is_estimate', true)
                ->where('stayCostSummary.final_nights', 1)
                ->where('stayCostSummary.final_amount', '100000.00')
                ->where('stayCostSummary.estimated_nights', 3)
                ->where('stayCostSummary.estimated_amount', '600000.00')
                ->where('stayCostSummary.total_nights', 4)
                ->where('stayCostSummary.total_amount', '700000.00')
                ->where('stayCostSummary.items.0.is_estimate', false)
                ->where('stayCostSummary.items.1.is_estimate', true)
                ->where('stayCostSummary.items.1.check_out_now_billable_nights', 2)
                ->where('stayCostSummary.items.1.check_out_now_subtotal_amount', '400000.00')
                ->where('stay.room_occupancies.0.events.0.type', RoomOccupancyEventType::CheckedOut->value)
                ->where('stay.room_occupancies.0.events.0.after_data.lodging_charge_policy', LodgingChargePolicy::ConsumedNights->value)
                ->where('stay.room_occupancies.0.events.0.user', null)
                ->has('stay.room_occupancies.0.guests', 1)
                ->has('stay.room_occupancies.1.guests', 1));
    }

    /** @return array{Hotel, Stay, RoomOccupancy, RoomOccupancy} */
    private function stayWithTwoRooms(): array
    {
        $referenceDate = now();
        $checkedInAt = $referenceDate->copy()->subDays(2)->setTime(14, 0);
        $expectedCheckOutOn = $referenceDate->copy()->addDay()->toDateString();
        $hotel = Hotel::factory()->create();
        $roomType = RoomType::factory()->for($hotel)->create(['capacity' => 2]);
        $firstRoom = Room::factory()->for($hotel)->for($roomType)->create(['number' => '201']);
        $secondRoom = Room::factory()->for($hotel)->for($roomType)->create(['number' => '202']);
        $responsibleGuest = Guest::factory()->for($hotel)->create();
        $companionGuest = Guest::factory()->for($hotel)->create();
        $stay = Stay::factory()->for($hotel)->create([
            'responsible_guest_id' => $responsibleGuest->id,
            'checked_in_at' => $checkedInAt,
            'expected_check_out_on' => $expectedCheckOutOn,
        ]);
        StayGuest::factory()->for($stay)->for($responsibleGuest)->create(['role' => StayGuestRole::Responsible]);
        StayGuest::factory()->for($stay)->for($companionGuest)->create(['role' => StayGuestRole::Companion]);
        $firstOccupancy = RoomOccupancy::factory()->for($stay)->for($firstRoom)->create([
            'nightly_rate' => '100000.00',
            'checked_in_at' => $checkedInAt,
            'expected_check_out_on' => $expectedCheckOutOn,
        ]);
        $secondOccupancy = RoomOccupancy::factory()->for($stay)->for($secondRoom)->create([
            'nightly_rate' => '200000.00',
            'checked_in_at' => $checkedInAt,
            'expected_check_out_on' => $expectedCheckOutOn,
        ]);
        $firstOccupancy->guests()->attach($responsibleGuest);
        $secondOccupancy->guests()->attach($companionGuest);

        return [$hotel, $stay, $firstOccupancy, $secondOccupancy];
    }

    private function settleOccupancy(RoomOccupancy $occupancy, CarbonInterface $checkedOutAt): void
    {
        $folio = app(CreateStayFolio::class)->execute($occupancy);
        $amount = app(CalculateOccupancyCharge::class)->execute($occupancy, $checkedOutAt->toImmutable());
        $folio->adjustments()->create([
            'type' => FolioAdjustmentType::Courtesy,
            'direction' => FolioAdjustmentDirection::Credit,
            'amount_minor' => $amount['total_amount_minor'],
            'reason' => 'Operational checkout test settlement',
            'occurred_at' => now(),
            'idempotency_key' => (string) Str::uuid(),
        ]);
    }
}
