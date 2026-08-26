<?php

declare(strict_types=1);

namespace Tests\Feature\Guests;

use App\Models\Guest;
use App\Models\Hotel;
use App\Models\IdentificationType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GuestStoreTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_a_guest_for_its_hotel(): void
    {
        $hotel = Hotel::factory()->create();
        $identificationType = IdentificationType::factory()->create();

        $this->post(route('hotels.guests.store', $hotel), $this->payload($identificationType))
            ->assertRedirect();

        $guest = Guest::query()->sole();
        $this->assertTrue($guest->hotel->is($hotel));
        $this->assertSame('123456789', $guest->identification_number);
    }

    #[Test]
    public function it_rejects_a_duplicate_identification_in_the_same_hotel(): void
    {
        $hotel = Hotel::factory()->create();
        $identificationType = IdentificationType::factory()->create();
        Guest::factory()->for($hotel)->create([
            'identification_type_id' => $identificationType->id,
            'identification_number' => '123456789',
        ]);

        $this->post(route('hotels.guests.store', $hotel), $this->payload($identificationType))
            ->assertSessionHasErrors('identification_number');
    }

    #[Test]
    public function it_allows_the_same_identification_at_another_hotel(): void
    {
        $identificationType = IdentificationType::factory()->create();
        $otherHotel = Hotel::factory()->create();
        Guest::factory()->for($otherHotel)->create([
            'identification_type_id' => $identificationType->id,
            'identification_number' => '123456789',
        ]);
        $hotel = Hotel::factory()->create();

        $this->post(route('hotels.guests.store', $hotel), $this->payload($identificationType))
            ->assertRedirect();
    }

    #[Test]
    public function it_updates_an_administrative_guest_profile(): void
    {
        $hotel = Hotel::factory()->create();
        $guest = Guest::factory()->for($hotel)->create();

        $this->put(route('hotels.guests.update', [$hotel, $guest]), [
            'identification_type_id' => $guest->identification_type_id,
            'first_name' => 'María',
            'last_name' => 'Gómez',
            'identification_number' => $guest->identification_number,
            'mobile' => '3001234567',
            'email' => 'maria@example.com',
        ])->assertRedirect();

        $this->assertSame('María', $guest->refresh()->first_name);
        $this->assertSame('maria@example.com', $guest->email);
    }

    /** @return array<string, mixed> */
    private function payload(IdentificationType $identificationType): array
    {
        return [
            'identification_type_id' => $identificationType->id,
            'first_name' => 'María',
            'last_name' => 'Gómez',
            'identification_number' => '123456789',
            'mobile' => '3001234567',
            'email' => 'maria@example.com',
        ];
    }
}
