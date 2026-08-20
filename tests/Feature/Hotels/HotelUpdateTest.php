<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HotelUpdateTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_a_hotel(): void
    {
        $hotel = Hotel::factory()->create();

        $response = $this->patch(route('hotels.update', $hotel), [
            'business_name' => 'Hotel Paradise Premium',
            'tin' => $hotel->tin,
            'email' => 'premium@example.com',
        ]);

        $response
            ->assertRedirect(route('hotels.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('hotels', [
            'id' => $hotel->id,
            'business_name' => 'Hotel Paradise Premium',
            'email' => 'premium@example.com',
        ]);
    }

    #[Test]
    public function it_keeps_its_own_tin_when_updating_a_hotel(): void
    {
        $hotel = Hotel::factory()->create();

        $this->patch(route('hotels.update', $hotel), [
            'business_name' => $hotel->business_name,
            'tin' => $hotel->tin,
        ])->assertSessionHasNoErrors();
    }

    #[Test]
    public function it_deletes_a_hotel(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('hotels/hotel.png', 'hotel image');

        $hotel = Hotel::factory()->create(['image' => 'hotels/hotel.png']);

        $response = $this->delete(route('hotels.destroy', $hotel));

        $response->assertRedirect(route('hotels.index'));

        $this->assertDatabaseMissing('hotels', ['id' => $hotel->id]);
        Storage::disk('public')->assertMissing('hotels/hotel.png');
    }

    #[Test]
    public function it_replaces_an_existing_hotel_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('hotels/previous.png', 'previous image');

        $hotel = Hotel::factory()->create(['image' => 'hotels/previous.png']);

        $this->patch(route('hotels.update', $hotel), [
            'business_name' => $hotel->business_name,
            'tin' => $hotel->tin,
            'image' => UploadedFile::fake()->image('hotel.webp')->size(512),
        ])->assertRedirect(route('hotels.index'));

        $imagePath = $hotel->refresh()->getRawOriginal('image');

        Storage::disk('public')->assertMissing('hotels/previous.png');
        Storage::disk('public')->assertExists($imagePath);
    }
}
