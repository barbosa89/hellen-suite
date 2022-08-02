<?php

namespace Tests\Feature\Assets\Maintenances;

use Tests\TestCase;
use App\Models\Room;
use App\Models\User;
use App\Models\Asset;
use App\Models\Hotel;
use App\Models\Maintenance;
use Tests\Traits\HasPermissions;
use Tests\Traits\HasFlashMessages;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AssetMaintenanceEditTest extends TestCase
{
    use WithFaker;
    use HasPermissions;
    use RefreshDatabase;
    use HasFlashMessages;

    private string $route;
    private User $user;
    private Hotel $hotel;
    private Room $room;
    private Asset $asset;

    private Maintenance $maintenance;

    public function setUp(): void
    {
        parent::setUp();

        $this->createPermission('assets.edit');

        $this->user = User::factory()->create();
        $this->user->givePermissionTo('assets.edit');

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

        $this->maintenance = Maintenance::factory()
            ->for($this->asset, 'maintainable')
            ->for($this->user)
            ->create();

        $this->route = route('assets.maintenances.edit', [
            'asset' => $this->asset->hash,
            'maintenance' => $this->maintenance->hash,
        ]);
    }

    public function test_guest_user_cannot_access_to_asset_maintenance_edition_form(): void
    {
        $response = $this->get($this->route);

        $response->assertRedirect(route('login'));
    }

    public function test_unauthorized_user_cannot_access_to_asset_maintenance_edition_form(): void
    {
        /** @var User $unauthorized */
        $unauthorized = User::factory()->create();

        $response = $this->actingAs($unauthorized)
            ->get($this->route);

        $response->assertForbidden();
    }

    public function test_authorized_user_can_access_to_asset_maintenance_edition_form(): void
    {
        $response = $this->actingAs($this->user)
            ->get($this->route);

        $response->assertOk()
            ->assertViewIs('app.assets.maintenances.edit')
            ->assertViewHas('maintenance', function (Maintenance $data) {
                return $data->is($this->maintenance);
            });
    }
}
