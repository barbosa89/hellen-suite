<?php

declare(strict_types=1);

namespace Tests\Feature\Hotels;

use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

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
