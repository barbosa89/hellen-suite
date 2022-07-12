<?php

namespace Tests\Feature\Assets;

use Tests\TestCase;
use App\Models\Room;
use App\Models\User;
use App\Models\Asset;
use App\Models\Hotel;
use Tests\Traits\HasPermissions;
use Illuminate\Support\Collection;
use Tests\Traits\HasFlashMessages;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AssetShowTest extends TestCase
{
    use HasPermissions;
    use RefreshDatabase;
    use HasFlashMessages;

    private const RESOURCE_NAME = 'assets.show';

    private string $route;
    private User $user;
    private Hotel $hotel;
    private Room $room;
    private Asset $asset;


    public function setUp(): void
    {
        parent::setUp();

        $this->createPermission(self::RESOURCE_NAME);

        $this->user = User::factory()->create();
        $this->user->givePermissionTo(self::RESOURCE_NAME);

        $this->hotel = Hotel::factory()
        ->for($this->user, 'owner')
        ->create();

        $this->room = Room::factory()
        ->for($this->hotel)
        ->for($this->user)
        ->create();

        $this->asset = Asset::factory()
        ->for($this->room)
        ->for($this->hotel)
        ->for($this->user)
        ->create();

        $this->route = route(self::RESOURCE_NAME, $this->asset->hash);
    }

    public function test_guest_user_cannot_access_to_asset_details(): void
    {
        $response = $this->get($this->route);

        $response->assertRedirect(route('login'));
    }

    public function test_unauthorized_user_cannot_access_to_asset_details(): void
    {
        /** @var User $unauthorized */
        $unauthorized = User::factory()->create();

        $response = $this->actingAs($unauthorized)
            ->get($this->route);

        $response->assertForbidden();
    }

    public function test_authorized_user_can_access_to_asset_details(): void
    {
        $response = $this->actingAs($this->user)
            ->get($this->route);

        $response->assertOk()
            ->assertViewIs('app.assets.show')
            ->assertViewHas('asset', function (Asset $data) {
                return $data->is($this->asset)
                    && $data->hotel->is($this->hotel)
                    && $data->room->is($this->room);
            });
    }
}
