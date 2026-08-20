<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

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
