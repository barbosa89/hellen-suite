<?php

namespace Tests\Feature\Assets\Maintenances;

use Tests\TestCase;
use App\Models\Room;
use App\Models\User;
use App\Models\Asset;
use App\Models\Hotel;
use App\Models\Maintenance;
use Tests\Traits\HasPermissions;
use Illuminate\Http\UploadedFile;
use Tests\Traits\HasFlashMessages;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AssetMaintenanceUpdateTest extends TestCase
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

        $this->route = route('assets.maintenances.update', [
            'asset' => $this->asset->hash,
            'maintenance' => $this->maintenance->hash,
        ]);
    }

    public function test_guest_user_cannot_update_an_asset_maintenance(): void
    {
        $response = $this->patch($this->route);

        $response->assertRedirect(route('login'));
    }

    public function test_unauthorized_user_cannot_update_an_asset_maintenance(): void
    {
        /** @var User $unauthorized */
        $unauthorized = User::factory()->create();

        $response = $this->actingAs($unauthorized)
            ->patch($this->route);

        $response->assertForbidden();
    }

    public function test_authorized_user_can_update_an_asset_maintenance(): void
    {
        $file = UploadedFile::fake()->create('document.pdf', 50);

        $data = [
            'date' => now()->format('Y-m-d'),
            'commentary' => $this->faker->sentence(3),
            'value' => $this->faker->randomNumber(4),
            'invoice' => $file,
        ];

        Storage::assertExists($this->maintenance->invoice);

        $response = $this->actingAs($this->user)
            ->patch($this->route, $data);

        $response->assertSessionDoesntHaveErrors()
            ->assertRedirect();

        $this->asssertFlashMessage(trans('common.updatedSuccessfully'), 'success');

        $this->assertDatabaseCount('maintenances', 1);

        $this->assertDatabaseHas('maintenances', [
            'id' => $this->maintenance->id,
            'date' => $data['date'],
            'value' => $data['value'],
            'commentary' => $data['commentary'],
            'invoice' => "public/{$file->hashName()}",
            'maintainable_id' => $this->asset->id,
            'maintainable_type' => Asset::class,
        ]);

        Storage::assertExists("public/{$file->hashName()}");
        Storage::assertMissing($this->maintenance->invoice);
    }
}
