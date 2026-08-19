<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Hotel;
use PHPUnit\Framework\Attributes\Test;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HotelShowTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_the_hotel_details(): void
    {
        $hotel = Hotel::factory()->create();

        $this->get(route('hotels.show', $hotel))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Hotels/Show')
                ->where('hotel.id', $hotel->getKey()));
    }
}
