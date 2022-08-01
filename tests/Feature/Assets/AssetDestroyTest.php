<?php

namespace Tests\Feature\Assets;

use Tests\TestCase;
use App\Models\Room;
use App\Models\User;
use App\Models\Asset;
use App\Models\Hotel;
use Tests\Traits\HasPermissions;
use Tests\Traits\HasFlashMessages;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AssetDestroyTest extends TestCase
{
    use WithFaker;
    use HasPermissions;
    use RefreshDatabase;
    use HasFlashMessages;

    private const RESOURCE_NAME = 'assets.destroy';

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

    public function test_guest_user_cannot_delete_an_asset(): void
    {
        $response = $this->get($this->route);

        $response->assertRedirect(route('login'));
    }

    public function test_unauthorized_user_cannot_delete_an_asset(): void
    {
        /** @var User $unauthorized */
        $unauthorized = User::factory()->create();

        $response = $this->actingAs($unauthorized)
            ->get($this->route);

        $response->assertForbidden();
    }

    public function test_authorized_user_can_delete_an_asset(): void
    {
        $response = $this->actingAs($this->user)
            ->delete($this->route);

        $response->assertSessionDoesntHaveErrors()
            ->assertRedirect(route('assets.index'));

        $this->asssertFlashMessage(trans('common.deletedSuccessfully'), 'success');

        $this->assertDatabaseMissing('assets', [
            'id' => $this->asset->id,
        ]);
    }
}
