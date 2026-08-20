<?php

namespace Tests\Feature;

use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HotelStoreTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_a_hotel(): void
    {
        $response = $this->post(route('hotels.store'), [
            'business_name' => 'Hotel Paradise',
            'tin' => '123456789',
            'address' => 'Calle 5, Bogotá',
            'phone' => '+5712345678',
            'mobile' => '+573001234567',
            'email' => 'paradise@example.com',
        ]);

        $response
            ->assertRedirect(route('hotels.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('hotels', [
            'business_name' => 'Hotel Paradise',
            'tin' => '123456789',
            'email' => 'paradise@example.com',
        ]);
    }

    #[Test]
    public function it_requires_hotel_name_and_tin_to_create_a_hotel(): void
    {
        $response = $this->post(route('hotels.store'), [
            'business_name' => '',
            'tin' => '',
            'email' => 'not-an-email',
        ]);

        $response->assertSessionHasErrors(['business_name', 'tin', 'email']);
    }

    #[Test]
    public function it_requires_tin_to_be_unique_when_creating_a_hotel(): void
    {
        Hotel::factory()->create(['tin' => '123456789']);

        $this->post(route('hotels.store'), [
            'business_name' => 'Hotel Duplicate',
            'tin' => '123456789',
        ])->assertSessionHasErrors('tin');
    }

    #[Test]
    public function it_stores_an_uploaded_hotel_image_publicly(): void
    {
        Storage::fake('public');

        $this->post(route('hotels.store'), [
            'business_name' => 'Hotel Paradise',
            'tin' => '123456789',
            'image' => UploadedFile::fake()->image('hotel.png')->size(512),
        ])->assertRedirect(route('hotels.index'));

        $hotel = Hotel::query()->where('tin', '123456789')->firstOrFail();
        $imagePath = $hotel->getRawOriginal('image');

        Storage::disk('public')->assertExists($imagePath);
        $this->assertSame(Storage::disk('public')->url($imagePath), $hotel->image);
    }

    #[Test]
    public function it_rejects_images_with_invalid_formats_or_sizes(): void
    {
        $this->post(route('hotels.store'), [
            'business_name' => 'Hotel GIF',
            'tin' => '123456789',
            'image' => UploadedFile::fake()->create('hotel.gif', 100, 'image/gif'),
        ])->assertSessionHasErrors('image');

        $this->post(route('hotels.store'), [
            'business_name' => 'Hotel Large',
            'tin' => '987654321',
            'image' => UploadedFile::fake()->image('hotel.jpg')->size(1025),
        ])->assertSessionHasErrors('image');
    }
}
