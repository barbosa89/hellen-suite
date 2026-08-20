<?php

declare(strict_types=1);

namespace Tests\Feature\Hotels;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

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
