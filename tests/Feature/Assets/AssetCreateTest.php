<?php

namespace Tests\Feature\Assets;

use Tests\TestCase;
use App\Models\User;
use App\Models\Asset;
use App\Models\Hotel;
use App\Models\Room;
use Tests\Traits\HasPermissions;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\HasFlashMessages;

class AssetCreateTest extends TestCase
{
    use HasPermissions;
    use RefreshDatabase;
    use HasFlashMessages;

    private const RESOURCE_NAME = 'assets.create';

    private string $route;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->createPermission(self::RESOURCE_NAME);

        $this->route = route(self::RESOURCE_NAME);

        $this->user = User::factory()->create();
        $this->user->givePermissionTo(self::RESOURCE_NAME);
    }

    public function test_guest_user_cannot_access_to_creating_form(): void
    {
        $response = $this->get($this->route);

        $response->assertRedirect(route('login'));
    }

    public function test_unauthorized_user_cannot_access_to_creating_form(): void
    {
        /** @var User $unauthorized */
        $unauthorized = User::factory()->create();

        $response = $this->actingAs($unauthorized)
            ->get($this->route);

        $response->assertForbidden();
    }

    public function test_authorized_user_is_redirected_when_there_are_no_hotels(): void
    {
        $response = $this->actingAs($this->user)
            ->get($this->route);

        $response->assertRedirect(route('hotels.index'));

        $this->asssertFlashMessage(trans('hotels.no.registered'), 'info');
    }

    public function test_authorized_user_is_redirected_when_there_are_no_hotel_rooms(): void
    {
        Hotel::factory()
            ->for($this->user, 'owner')
            ->create();

        $response = $this->actingAs($this->user)
            ->get($this->route);

        $response->assertRedirect(route('assets.index'));

        $this->asssertFlashMessage(trans('rooms.no.created'), 'info');
    }

    public function test_authorized_user_can_access_to_creating_form(): void
    {
        $hotel = Hotel::factory()
            ->for($this->user, 'owner')
            ->create();

        Room::factory()
            ->for($hotel)
            ->for($this->user)
            ->create();

        $asset = Asset::factory()
            ->for($hotel, 'hotel')
            ->create();

        $response = $this->actingAs($this->user)
            ->get($this->route);

        $response->assertOk()
            ->assertViewIs('app.assets.create')
            ->assertViewHas('hotels', function (Collection $hotels) use ($hotel, $asset) {
                return $hotels->first()->is($hotel)
                    && $hotels->first()->assets->first()->is($asset);
            });
    }
}
