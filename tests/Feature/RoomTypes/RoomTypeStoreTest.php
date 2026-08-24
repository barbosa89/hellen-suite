<?php

declare(strict_types=1);

namespace Tests\Feature\RoomTypes;

use App\Models\Hotel;
use App\Models\RoomType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RoomTypeStoreTest extends TestCase
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
}
