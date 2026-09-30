<?php

declare(strict_types=1);

namespace Tests\Feature\Stays;

use App\Actions\Stays\CreateStayFolio;
use App\Constants\FolioAdjustmentType;
use App\Constants\HousekeepingStatus;
use App\Constants\StayStatus;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomOccupancy;
use App\Models\RoomType;
use App\Models\Stay;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StaySettlementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function checkout_rolls_back_when_the_folio_has_a_balance(): void
    {
        [$hotel, $stay, $occupancy] = $this->activeStay();

        $this->post(route('hotels.stays.check-out', [$hotel, $stay]))->assertSessionHasErrors('payment');

        $this->assertSame(StayStatus::Active, $stay->refresh()->status);
        $this->assertNull($occupancy->refresh()->checked_out_at);
        $this->assertSame(HousekeepingStatus::Clean, $occupancy->room->refresh()->housekeeping_status);
        $this->assertSame(0, $occupancy->charges()->count());
    }

    #[Test]
    public function courtesy_settles_the_folio_without_moving_cash(): void
    {
        [$hotel, $stay, $occupancy] = $this->activeStay();
        $folio = app(CreateStayFolio::class)->execute($occupancy);

        $this->post(route('hotels.stays.folios.adjustments.store', [$hotel, $stay, $folio]), [
            'type' => FolioAdjustmentType::Courtesy->value,
            'amount' => '1000.00',
            'reason' => 'Manager courtesy',
        ])->assertSessionHasNoErrors();
        $this->post(route('hotels.stays.check-out', [$hotel, $stay]))->assertSessionHasNoErrors();

        $this->assertSame(StayStatus::CheckedOut, $stay->refresh()->status);
        $this->assertNotNull($folio->refresh()->closed_at);
        $this->assertSame(0, $hotel->cashMovements()->count());
    }

    #[Test]
    public function adjustment_cannot_exceed_the_projected_balance(): void
    {
        [$hotel, $stay, $occupancy] = $this->activeStay();
        $folio = app(CreateStayFolio::class)->execute($occupancy);

        $this->post(route('hotels.stays.folios.adjustments.store', [$hotel, $stay, $folio]), [
            'type' => FolioAdjustmentType::Discount->value,
            'amount' => '1000.01',
            'reason' => 'Excessive discount',
        ])->assertSessionHasErrors('amount');

        $this->assertSame(0, $folio->adjustments()->count());
    }

    #[Test]
    public function manual_charge_cannot_be_added_to_a_closed_folio(): void
    {
        [$hotel, $stay, $occupancy] = $this->activeStay();
        $folio = app(CreateStayFolio::class)->execute($occupancy);
        $folio->update(['closed_at' => now()]);

        $this->post(route('hotels.stays.folios.charges.store', [$hotel, $stay, $folio]), [
            'amount' => '10.00',
            'description' => 'Late charge',
        ])->assertSessionHasErrors('charge');

        $this->assertSame(0, $folio->charges()->count());
    }

    #[Test]
    public function same_day_transfer_keeps_one_folio_without_adding_an_extra_night(): void
    {
        $this->travelTo(now()->startOfDay()->addHours(12));
        [$hotel, $stay, $occupancy] = $this->activeStay();
        $occupancy->update(['checked_in_at' => now()->subDays(2)]);
        $folio = app(CreateStayFolio::class)->execute($occupancy);
        $newRoom = Room::factory()->for($hotel)->for(RoomType::factory()->for($hotel))->create();

        $this->post(route('hotels.stays.room-occupancies.transfer', [$hotel, $stay, $occupancy]), [
            'room_id' => $newRoom->id,
            'nightly_rate' => '1000.00',
        ])->assertSessionHasNoErrors();

        $newOccupancy = $stay->roomOccupancies()->latest('id')->firstOrFail();
        $this->assertSame($folio->id, $newOccupancy->stay_folio_id);
        $this->assertSame(200000, $folio->charges()->sole()->total_amount_minor);

        $this->post(route('hotels.stays.folios.adjustments.store', [$hotel, $stay, $folio]), [
            'type' => FolioAdjustmentType::Courtesy->value,
            'amount' => '2000.00',
            'reason' => 'Manager courtesy',
        ])->assertSessionHasNoErrors();
        $this->post(route('hotels.stays.check-out', [$hotel, $stay]))->assertSessionHasNoErrors();

        $this->assertSame(1, $folio->charges()->count());
        $this->assertSame(StayStatus::CheckedOut, $stay->refresh()->status);
    }

    /** @return array{Hotel, Stay, RoomOccupancy} */
    private function activeStay(): array
    {
        $hotel = Hotel::factory()->create();
        $guest = Guest::factory()->for($hotel)->create();
        $stay = Stay::factory()->for($hotel)->create(['responsible_guest_id' => $guest->id]);
        $roomType = RoomType::factory()->for($hotel)->create();
        $room = Room::factory()->for($hotel)->for($roomType)->create();
        $occupancy = RoomOccupancy::factory()->for($stay)->for($room)->create(['nightly_rate' => '1000.00']);

        return [$hotel, $stay, $occupancy];
    }
}
