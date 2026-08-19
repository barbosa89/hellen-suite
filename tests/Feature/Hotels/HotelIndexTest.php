<?php

namespace Tests\Feature\Hotels;

use Tests\TestCase;
use App\Models\Hotel;
use PHPUnit\Framework\Attributes\Test;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HotelIndexTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_paginated_hotels(): void
    {
        Hotel::factory(12)->create();

        $response = $this->get(route('hotels.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Hotels/Index')
                ->has('hotels.data', 10)
                ->where('hotels.last_page', 2)
                ->has('hotels.links'));
    }
}
