<?php

namespace Tests\Feature\Assets\Maintenances;

use App\Models\Asset;
use App\Models\Hotel;
use App\Models\Maintenance;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Traits\HasFlashMessages;
use Tests\Traits\HasPermissions;

class AssetMaintenanceDestroyTest extends TestCase
{
    use HasFlashMessages;
    use HasPermissions;
    use RefreshDatabase;
    use WithFaker;

    private string $route;

    private User $user;

    private Hotel $hotel;

    private Room $room;

    private Asset $asset;

    private Maintenance $maintenance;

    public function setUp(): void
    {
        parent::setUp();

        Storage::fake();

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

        $this->route = route('assets.maintenances.destroy', [
            'asset' => $this->asset->hash,
            'maintenance' => $this->maintenance->hash,
        ]);
    }

    public function test_guest_user_cannot_delete_an_asset_maintenance(): void
    {
        $response = $this->delete($this->route);

        $response->assertRedirect(route('login'));
    }

    public function test_unauthorized_user_cannot_delete_an_asset_maintenance(): void
    {
        /** @var User $unauthorized */
        $unauthorized = User::factory()->create();

        $response = $this->actingAs($unauthorized)
            ->delete($this->route);

        $response->assertForbidden();
    }

    public function test_authorized_user_can_delete_an_asset_maintenance(): void
    {
        $response = $this->actingAs($this->user)
            ->delete($this->route);

        $response->assertSessionDoesntHaveErrors()
            ->assertRedirect();

        $this->asssertFlashMessage(trans('common.deletedSuccessfully'), 'success');

        $this->assertDatabaseCount('maintenances', 0);

        $this->assertDatabaseMissing('maintenances', [
            'id' => $this->maintenance->id,
        ]);

        Storage::assertMissing($this->maintenance->invoice);
    }
}
