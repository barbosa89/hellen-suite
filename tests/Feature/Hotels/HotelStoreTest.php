<?php

namespace Tests\Feature\Hotels;

use App\Constants\HotelType;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\Traits\HasFlashMessages;
use Tests\Traits\HasPermissions;

class HotelStoreTest extends TestCase
{
    use HasFlashMessages;
    use HasPermissions;
    use RefreshDatabase;

    private string $route;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->createPermission('hotels.create');

        $this->route = route('hotels.store');

        $this->user = User::factory()->create();
        $this->user->givePermissionTo('hotels.create');
    }

    public function test_guest_user_cannot_create_hotels(): void
    {
        $response = $this->post($this->route);

        $response->assertRedirect(route('login'));
    }

    public function test_unauthorized_user_cannot_create_hotels(): void
    {
        /** @var User $unauthorized */
        $unauthorized = User::factory()->create();

        $response = $this->actingAs($unauthorized)
            ->post($this->route);

        $response->assertForbidden();
    }

    public function test_authorized_user_can_create_hotels(): void
    {
        Storage::fake('public');

        $hotel = Hotel::factory()
            ->for($this->user, 'owner')
            ->make();

        $image = UploadedFile::fake()->image('logo.jpg', 100, 100);

        $data = [
            'business_name' => $hotel->business_name,
            'tin' => Str::random(8),
            'address' => $hotel->address,
            'phone' => $hotel->phone,
            'mobile' => $hotel->mobile,
            'image' => $image,
            'email' => $hotel->email,
            'type' => HotelType::MAIN->value,
        ];

        $response = $this->actingAs($this->user)
            ->post($this->route, $data);

        $response->assertSessionDoesntHaveErrors()
            ->assertRedirect();

        $this->asssertFlashMessage(trans('common.createdSuccessfully'), 'success');

        $this->assertDatabaseCount('hotels', 1);

        $data['image'] = $image->hashName();

        $this->assertDatabaseHas('hotels', Arr::except($data, ['type']));

        Storage::disk('public')->assertExists($image->hashName());
    }

    public function test_it_ignores_main_hotel_field_when_type_is_main(): void
    {
        $mainHotel = Hotel::factory()
            ->for($this->user, 'owner')
            ->create();

        $hotel = Hotel::factory()
            ->for($this->user, 'owner')
            ->make();

        $data = [
            'business_name' => $hotel->business_name,
            'tin' => Str::random(8),
            'address' => $hotel->address,
            'phone' => $hotel->phone,
            'mobile' => $hotel->mobile,
            'email' => $hotel->email,
            'type' => HotelType::MAIN->value,
            'main_hotel' => $mainHotel->hash,
        ];

        $response = $this->actingAs($this->user)
            ->post($this->route, $data);

        $response->assertSessionDoesntHaveErrors()
            ->assertRedirect();

        $this->asssertFlashMessage(trans('common.createdSuccessfully'), 'success');

        $this->assertDatabaseCount('hotels', 2);

        $data['main_hotel'] = null;

        $this->assertDatabaseHas('hotels', Arr::except($data, ['type']));
    }

    public function test_it_create_hotel_headquarter(): void
    {
        $mainHotel = Hotel::factory()
            ->for($this->user, 'owner')
            ->create();

        $hotel = Hotel::factory()
            ->for($this->user, 'owner')
            ->make();

        $data = [
            'business_name' => $hotel->business_name,
            'tin' => $mainHotel->tin,
            'address' => $hotel->address,
            'phone' => $hotel->phone,
            'mobile' => $hotel->mobile,
            'email' => $hotel->email,
            'type' => HotelType::HEADQUARTERS->value,
            'main_hotel' => $mainHotel->hash,
        ];

        $response = $this->actingAs($this->user)
            ->post($this->route, $data);

        $response->assertSessionDoesntHaveErrors()
            ->assertRedirect();

        $this->asssertFlashMessage(trans('common.createdSuccessfully'), 'success');

        $this->assertDatabaseCount('hotels', 2);

        $data['main_hotel'] = $mainHotel->id;

        $this->assertDatabaseHas('hotels', Arr::except($data, ['type']));
    }
}
