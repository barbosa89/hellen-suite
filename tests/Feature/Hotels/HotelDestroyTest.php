<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Hotel;
use PHPUnit\Framework\Attributes\Test;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
