<?php

declare(strict_types=1);

namespace Tests\Feature\Rooms;

use App\Constants\HousekeepingStatus;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomType;
use App\Settings\GeneralSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RoomStoreTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_a_room_for_its_hotel(): void
    {
        GeneralSettings::fake(['currency' => 'COP']);
        $hotel = Hotel::factory()->create();
        $roomType = RoomType::factory()->for($hotel)->create();

        $this->post(route('hotels.rooms.store', $hotel), [
            'room_type_id' => $roomType->id,
            'number' => '101',
            'floor' => '1',
            'reference_price' => '125000.50',
            'housekeeping_status' => HousekeepingStatus::Clean->value,
            'is_active' => true,
        ])->assertRedirect(route('hotels.rooms.index', $hotel));

        $room = Room::query()->sole();
        $this->assertTrue($room->hotel->is($hotel));
        $this->assertTrue($room->roomType->is($roomType));
        $this->assertSame('125000.50', $room->reference_price);
    }

    #[Test]
    public function it_rejects_a_room_type_from_another_hotel(): void
    {
        GeneralSettings::fake(['currency' => 'COP']);
        $hotel = Hotel::factory()->create();
        $foreignRoomType = RoomType::factory()->create();

        $this->post(route('hotels.rooms.store', $hotel), [
            'room_type_id' => $foreignRoomType->id,
            'number' => '101',
            'reference_price' => '125000.50',
            'housekeeping_status' => HousekeepingStatus::Clean->value,
            'is_active' => true,
        ])->assertSessionHasErrors('room_type_id');
    }

    #[Test]
    public function it_requires_a_currency_before_creating_a_room(): void
    {
        GeneralSettings::fake(['currency' => null]);
        $hotel = Hotel::factory()->create();

        $this->get(route('hotels.rooms.create', $hotel))
            ->assertRedirect(route('settings.edit'))
            ->assertSessionHas('error');
    }

    #[Test]
    public function it_updates_and_deletes_a_room(): void
    {
        GeneralSettings::fake(['currency' => 'COP']);
        $hotel = Hotel::factory()->create();
        $room = Room::factory()->for($hotel)->create(['number' => '101']);

        $this->put(route('hotels.rooms.update', [$hotel, $room]), [
            'room_type_id' => $room->room_type_id,
            'number' => '102',
            'floor' => '2',
            'reference_price' => '150000.00',
            'housekeeping_status' => HousekeepingStatus::Dirty->value,
            'is_active' => false,
        ])->assertRedirect(route('hotels.rooms.index', $hotel));

        $this->assertSame('102', $room->refresh()->number);

        $this->delete(route('hotels.rooms.destroy', [$hotel, $room]))
            ->assertRedirect(route('hotels.rooms.index', $hotel));

        $this->assertModelMissing($room);
    }
}
