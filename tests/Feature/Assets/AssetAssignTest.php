<?php

namespace Tests\Feature\Assets;

use Tests\TestCase;
use App\Models\Room;
use App\Models\User;
use App\Models\Asset;
use App\Models\Hotel;
use Tests\Traits\HasPermissions;
use Tests\Traits\HasFlashMessages;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;

class AssetAssignTest extends TestCase
{
    use HasPermissions;
    use RefreshDatabase;
    use HasFlashMessages;

    private User $user;
    private Hotel $hotel;
    private Room $room;
    private Asset $asset;

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
            ->for($this->hotel)
            ->for($this->user)
            ->create();
    }

    public function test_guest_user_cannot_access_to_assignment_form(): void
    {
        $response = $this->get(route('assets.assignment', ['room' => $this->room->hash]));

        $response->assertRedirect(route('login'));
    }

    public function test_unauthorized_user_cannot_access_to_assignment_form(): void
    {
        /** @var User $unauthorized */
        $unauthorized = User::factory()->create();

        $response = $this->actingAs($unauthorized)
            ->get(route('assets.assignment', ['room' => $this->room->hash]));

        $response->assertForbidden();
    }

    public function test_authorized_user_can_access_to_assignment_form(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('assets.assignment', ['room' => $this->room->hash]));

        $response->assertOk()
            ->assertViewIs('app.assets.assign')
            ->assertViewHas('assets', function (Collection $data) {
                return $data->first()->is($this->asset);
            })
            ->assertViewHas('room', function (Room $data) {
                return $data->is($this->room);
            });
    }

    public function test_guest_user_cannot_assign_asset_to_room(): void
    {
        $response = $this->post(route('assets.assign', ['room' => $this->room->hash]));

        $response->assertRedirect(route('login'));
    }

    public function test_unauthorized_user_cannot_assign_asset_to_room(): void
    {
        /** @var User $unauthorized */
        $unauthorized = User::factory()->create();

        $response = $this->actingAs($unauthorized)
            ->post(route('assets.assign', ['room' => $this->room->hash]));

        $response->assertForbidden();
    }

    public function test_authorized_user_can_assign_asset_to_room(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('assets.assign', ['room' => $this->room->hash]), [
                'asset' => $this->asset->hash,
            ]);

        $response->assertSessionDoesntHaveErrors()
            ->assertRedirect(route('assets.assignment', [
                'room' => $this->room->hash,
            ]));

        $this->asssertFlashMessage(trans('common.updatedSuccessfully'), 'success');

        $this->assertDatabaseHas('assets', [
            'id' => $this->asset->id,
            'room_id' => $this->room->id,
        ]);
    }
}
