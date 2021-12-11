<?php

namespace Tests\Feature\Hotel\Setting;

use App\Constants\Modules;
use App\User;
use Tests\TestCase;
use App\Models\Hotel;
use RolesTableSeeder;
use App\Constants\Roles;
use ConfigurationsTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SettingIndexTest extends TestCase
{
    use RefreshDatabase;

    private User $manager;
    private Hotel $hotel;
    private string $route;
    private string $view = 'app.hotels.settings.index';

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesTableSeeder::class);
        $this->seed(ConfigurationsTableSeeder::class);

        $this->manager = factory(User::class)->create();
        $this->manager->assignRole(Roles::MANAGER);

        $this->hotel = factory(Hotel::class)->create([
            'user_id' => $this->manager->id,
        ]);

        $this->route = "/hotels/{$this->hotel->hash}/settings";
    }

    public function test_guest_user_is_redirected_to_login()
    {
        $response = $this->get($this->route);

        $response->assertRedirect('/login');
    }

    public function test_user_cannot_access_without_permissions()
    {
        /** @var \App\User $user */
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->get($this->route);

        $response->assertForbidden();
    }

    public function test_user_can_access_to_setting_list()
    {
        $response = $this->actingAs($this->manager)
            ->get($this->route);

        $response->assertViewIs($this->view)
            ->assertViewHas('hotel', function ($data) {
                return $data->id === $this->hotel->id;
            })
            ->assertViewHas('configurations', function ($data) {
                return $data->count() > 0
                    && $data->where('module', Modules::HOTELS)->count() === 1;
            });
    }
}
