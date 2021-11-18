<?php

namespace Tests\Feature\Hotel;

use App\User;
use Tests\TestCase;
use App\Models\Hotel;
use PermissionsTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HotelIndexTest extends TestCase
{
    use RefreshDatabase;

    private const ROUTE = '/hotels';
    private const VIEW = 'app.hotels.index';

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionsTableSeeder::class);
    }

    public function test_guest_user_is_redirected_to_login()
    {
        $response = $this->get(self::ROUTE);

        $response->assertRedirect('/login');
    }

    public function test_user_cannot_access_without_permissions()
    {
        /** @var \App\User $user */
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->get(self::ROUTE);

        $response->assertForbidden();
    }

    public function test_user_can_access_to_hotel_list()
    {
        /** @var \App\User $user */
        $user = factory(User::class)->create();
        $user->givePermissionTo('hotels.index');

        /** @var \App\Models\Hotel $hotel */
        $hotel = factory(Hotel::class)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->get(self::ROUTE);

        $response->assertOk()
            ->assertViewIs(self::VIEW)
            ->assertViewHas('hotels', function ($data) use ($hotel) {
                return $data->first()->id === $hotel->id;
            });
    }

    public function test_user_can_access_to_own_hotels_only()
    {
        /** @var \App\User $owner */
        $owner = factory(User::class)->create();
        $owner->givePermissionTo('hotels.index');

        factory(Hotel::class)->create([
            'user_id' => $owner->id,
        ]);

        /** @var \App\User $user */
        $user = factory(User::class)->create();
        $user->givePermissionTo('hotels.index');

        $response = $this->actingAs($user)
            ->get(self::ROUTE);

        $response->assertOk()
            ->assertViewIs(self::VIEW)
            ->assertViewHas('hotels', fn ($data) => $data->isEmpty());
    }
}
