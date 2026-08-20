<?php

namespace Tests\Feature;

use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HotelUpdateTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_a_hotel(): void
    {
        $hotel = Hotel::factory()->create();

        $response = $this->patch(route('hotels.update', $hotel), [
            'business_name' => 'Hotel Paradise Premium',
            'tin' => $hotel->tin,
            'email' => 'premium@example.com',
        ]);

        $response
            ->assertRedirect(route('hotels.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('hotels', [
            'id' => $hotel->id,
            'business_name' => 'Hotel Paradise Premium',
            'email' => 'premium@example.com',
        ]);
    }

    #[Test]
    public function it_keeps_its_own_tin_when_updating_a_hotel(): void
    {
        $hotel = Hotel::factory()->create();

        $this->patch(route('hotels.update', $hotel), [
            'business_name' => $hotel->business_name,
            'tin' => $hotel->tin,
        ])->assertSessionHasNoErrors();
    }

    #[Test]
    public function it_deletes_a_hotel(): void
    {
        $hotel = Hotel::factory()->create();

        $response = $this->delete(route('hotels.destroy', $hotel));

        $response->assertRedirect(route('hotels.index'));

        $this->assertDatabaseMissing('hotels', ['id' => $hotel->id]);
    }
}
