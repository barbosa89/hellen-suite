<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HotelDestroyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_deletes_a_hotel(): void
    {
        $hotel = Hotel::factory()->create();

        $response = $this->delete(route('hotels.destroy', $hotel));

        $response->assertRedirect(route('hotels.index'));

        $this->assertDatabaseMissing('hotels', ['id' => $hotel->id]);
    }
}
