<?php

namespace Tests\Feature\Assets;

use Tests\TestCase;
use App\Models\User;
use App\Models\Asset;
use App\Models\Hotel;
use Tests\Traits\HasPermissions;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\HasFlashMessages;

class AssetExportTest extends TestCase
{
    use HasPermissions;
    use RefreshDatabase;
    use HasFlashMessages;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->createPermission('assets.index');

        $this->user = User::factory()->create();
        $this->user->givePermissionTo('assets.index');
    }

    public function test_guest_user_cannot_access_to_assets_export_form(): void
    {
        $response = $this->get(route('assets.export.form'));

        $response->assertRedirect(route('login'));
    }

    public function test_unauthorized_user_cannot_access_to_assets_export_form(): void
    {
        /** @var User $unauthorized */
        $unauthorized = User::factory()->create();

        $response = $this->actingAs($unauthorized)
            ->get(route('assets.export.form'));

        $response->assertForbidden();
    }

    public function test_authorized_user_is_redirected_when_there_are_no_hotels(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('assets.export.form'));

        $response->assertRedirect(route('hotels.index'));

        $this->asssertFlashMessage(trans('hotels.no.registered'), 'info');
    }

    public function test_authorized_user_can_access_to_assets_export_form(): void
    {
        $hotel = Hotel::factory()
            ->for($this->user, 'owner')
            ->create();

        $response = $this->actingAs($this->user)
            ->get(route('assets.export.form'));

        $response->assertOk()
            ->assertViewIs('app.assets.export')
            ->assertViewHas('hotels', function (Collection $hotels) use ($hotel) {
                return $hotels->count() === 1 &&
                    $hotels->first()->is($hotel);
            });
    }

    public function test_authorized_user_can_export_all_assets(): void
    {
        $hotel = Hotel::factory()
            ->for($this->user, 'owner')
            ->create();

        Asset::factory()
            ->for($hotel)
            ->for($this->user)
            ->create();

        $response = $this->actingAs($this->user)
            ->post(route('assets.export'), [
                'type' => 'all',
            ]);

        $response->assertOk()
            ->assertHeader('Content-Disposition', 'attachment; filename=Assets.xlsx');;
    }

    public function test_authorized_user_can_export_assets_by_hotel(): void
    {
        $hotel = Hotel::factory()
            ->for($this->user, 'owner')
            ->create();

        Asset::factory()
            ->for($hotel)
            ->for($this->user)
            ->create();

        $response = $this->actingAs($this->user)
            ->post(route('assets.export'), [
                'type' => 'all',
                'hotel' => $hotel->hash,
            ]);

        $response->assertOk()
            ->assertHeader('Content-Disposition', 'attachment; filename=Assets.xlsx');;
    }
}
