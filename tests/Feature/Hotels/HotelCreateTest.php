<?php

namespace Tests\Feature\Hotels;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HotelCreateTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_hotel_create_form(): void
    {
        $this->get(route('hotels.create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Hotels/Create'));
    }
}
