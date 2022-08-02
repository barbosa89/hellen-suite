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
use Illuminate\Foundation\Testing\WithFaker;

class AssetUpdateTest extends TestCase
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

        $this->route = route('assets.update', $this->asset->hash);
    }

    public function test_guest_user_cannot_update_an_asset(): void
    {
        $response = $this->get($this->route);

        $response->assertRedirect(route('login'));
    }

    public function test_unauthorized_user_cannot_update_an_asset(): void
    {
        /** @var User $unauthorized */
        $unauthorized = User::factory()->create();

        $response = $this->actingAs($unauthorized)
            ->get($this->route);

        $response->assertForbidden();
    }

    public function test_authorized_user_can_update_an_asset(): void
    {
        $data = [
            'number' => $this->asset->number,
            'description' => $this->faker->sentence(),
            'brand' => $this->asset->brand,
            'model' => $this->asset->model,
            'serial_number' => $this->asset->serial_number,
            'price' => $this->faker->numberBetween(100, 200),
            'location' => $this->asset->location,
            'assign' => 'room',
            'room' => $this->room->hash,
            'hotel' => $this->hotel->hash,
        ];

        $response = $this->actingAs($this->user)
            ->put($this->route, $data);

        $response->assertSessionDoesntHaveErrors()
            ->assertRedirect(route('assets.show', ['id' => $this->asset->hash]));

        $this->asssertFlashMessage(trans('common.updatedSuccessfully'), 'success');

        $this->assertDatabaseCount('assets', 1);

        $this->assertDatabaseHas('assets', [
            'number' => $this->asset->number,
            'description' => $data['description'],
            'brand' => $this->asset->brand,
            'model' => $this->asset->model,
            'serial_number' => $this->asset->serial_number,
            'price' => (float) $data['price'],
            'location' => $this->asset->location,
            'room_id' => $this->room->id,
            'hotel_id' => $this->hotel->id,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_authorized_user_can_update_an_asset_with_any_assignation(): void
    {
        $data = [
            'number' => $this->asset->number,
            'description' => $this->faker->sentence(),
            'brand' => $this->asset->brand,
            'model' => $this->asset->model,
            'serial_number' => $this->asset->serial_number,
            'price' => $this->faker->numberBetween(100, 200),
            'location' => $this->asset->location,
            'assign' => 'any',
            'room' => $this->room->hash,
            'hotel' => $this->hotel->hash,
        ];

        $response = $this->actingAs($this->user)
            ->put($this->route, $data);

        $response->assertSessionDoesntHaveErrors()
            ->assertRedirect(route('assets.show', ['id' => $this->asset->hash]));

        $this->asssertFlashMessage(trans('common.updatedSuccessfully'), 'success');

        $this->assertDatabaseCount('assets', 1);

        $this->assertDatabaseHas('assets', [
            'number' => $this->asset->number,
            'description' => $data['description'],
            'brand' => $this->asset->brand,
            'model' => $this->asset->model,
            'serial_number' => $this->asset->serial_number,
            'price' => (float) $data['price'],
            'location' => $this->asset->location,
            'room_id' => null,
            'hotel_id' => $this->hotel->id,
            'user_id' => $this->user->id,
        ]);
    }
}
