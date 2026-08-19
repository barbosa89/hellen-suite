<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Hotel;
use PHPUnit\Framework\Attributes\Test;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HotelEditTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_hotel_edit_form(): void
    {
        $hotel = Hotel::factory()->create();

        $this->get(route('hotels.edit', $hotel))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Hotels/Edit')
                ->where('hotel.id', $hotel->getKey()));
    }
}
