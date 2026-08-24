<?php

declare(strict_types=1);

namespace Tests\Feature\Rooms;

use App\Constants\HousekeepingStatus;
use App\Models\Hotel;
use App\Models\Room;
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
