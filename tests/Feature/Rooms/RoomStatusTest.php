<?php

declare(strict_types=1);

namespace Tests\Feature\Rooms;

use App\Constants\HousekeepingStatus;
use App\Models\Hotel;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RoomStatusTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_housekeeping_status_and_activity(): void
    {
        $hotel = Hotel::factory()->create();
        $room = Room::factory()->for($hotel)->create([
            'housekeeping_status' => HousekeepingStatus::Dirty,
            'is_active' => true,
        ]);

        $this->patch(route('hotels.rooms.housekeeping-status.update', [$hotel, $room]), [
            'housekeeping_status' => HousekeepingStatus::Clean->value,
        ])->assertRedirect(route('hotels.rooms.index', $hotel));

        $this->patch(route('hotels.rooms.toggle', [$hotel, $room]))
            ->assertRedirect(route('hotels.rooms.index', $hotel));

        $room->refresh();
        $this->assertSame(HousekeepingStatus::Clean, $room->housekeeping_status);
        $this->assertFalse($room->is_active);
    }

    #[Test]
    public function it_does_not_bind_a_room_from_another_hotel(): void
    {
        $hotel = Hotel::factory()->create();
        $room = Room::factory()->create();

        $this->patch(route('hotels.rooms.toggle', [$hotel, $room]))->assertNotFound();
    }
}
