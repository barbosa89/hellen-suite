<?php

declare(strict_types=1);

namespace Tests\Feature\RoomTypes;

use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RoomTypeDestroyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_a_hotel_scoped_room_type(): void
    {
        $hotel = Hotel::factory()->create();

        $this->post(route('hotels.room-types.store', $hotel), [
            'name' => 'Suite familiar',
            'capacity' => 4,
        ])->assertRedirect(route('hotels.room-types.index', $hotel));

        $this->assertTrue(RoomType::query()->sole()->hotel->is($hotel));
    }

    #[Test]
    public function it_prevents_deleting_a_type_that_has_rooms(): void
    {
        $hotel = Hotel::factory()->create();
        $roomType = RoomType::factory()->for($hotel)->create();
        Room::factory()->for($hotel)->for($roomType)->create();

        $this->delete(route('hotels.room-types.destroy', [$hotel, $roomType]))
            ->assertRedirect(route('hotels.room-types.index', $hotel))
            ->assertSessionHas('error');

        $this->assertModelExists($roomType);
    }

    #[Test]
    public function it_deletes_an_empty_room_type(): void
    {
        $hotel = Hotel::factory()->create();
        $roomType = RoomType::factory()->for($hotel)->create();

        $this->delete(route('hotels.room-types.destroy', [$hotel, $roomType]))
            ->assertRedirect(route('hotels.room-types.index', $hotel));

        $this->assertModelMissing($roomType);
    }
}
