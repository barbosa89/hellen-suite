<?php

declare(strict_types=1);

namespace Tests\Feature\Guests;

use App\Models\Guest;
use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GuestIndexTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_lists_only_guests_for_the_selected_hotel(): void
    {
        $hotel = Hotel::factory()->create();
        $guest = Guest::factory()->for($hotel)->create(['last_name' => 'Álvarez']);
        Guest::factory()->create(['last_name' => 'Álvarez']);

        $this->get(route('hotels.guests.index', $hotel))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Hotels/Guests/Index')
                ->where('hotel.id', $hotel->id)
                ->has('guests.data', 1)
                ->where('guests.data.0.id', $guest->id));
    }

    #[Test]
    public function it_searches_hotel_guests_by_their_identification_number(): void
    {
        $hotel = Hotel::factory()->create();
        $guest = Guest::factory()->for($hotel)->create(['identification_number' => '987654321']);
        Guest::factory()->for($hotel)->create(['identification_number' => '123456789']);

        $this->get(route('hotels.guests.index', ['hotel' => $hotel, 'search' => '987654']))
            ->assertInertia(fn (Assert $page) => $page
                ->has('guests.data', 1)
                ->where('guests.data.0.id', $guest->id));
    }

    #[Test]
    public function it_does_not_register_a_guest_destroy_route(): void
    {
        $this->assertFalse(Route::has('hotels.guests.destroy'));
    }
}
