<?php

declare(strict_types=1);

namespace Tests\Feature\Hotels;

use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

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
