<?php

namespace Tests\Feature\Control\Configuration;

use App\Constants\Config;
use App\Constants\Roles;
use App\Models\Configuration;
use App\User;
use ConfigurationsTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use RolesTableSeeder;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    private const ROUTE = '/configurations';
    private const VIEW = 'control.configurations.index';

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesTableSeeder::class);
    }

    public function test_guest_user_is_redirected_to_login()
    {
        $response = $this->get(self::ROUTE);

        $response->assertRedirect('/login');
    }

    public function test_user_cannot_access_without_root_role()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
            ->get(self::ROUTE);

        $response->assertForbidden();
    }

    public function test_user_can_access_with_root_role()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->assignRole(Roles::ROOT);

        /** @var Configuration $configuration */
        $configuration = factory(Configuration::class)->create();

        $response = $this->actingAs($user)
            ->get(self::ROUTE);

        $response->assertOk()
            ->assertViewIs(self::VIEW)
            ->assertViewHas('configurations', function ($data) use ($configuration) {
                return $data->first()->id === $configuration->id;
            })
            ->assertDontSeeText(now()->format('Y-m-d'));
    }

    public function test_user_can_see_config_list()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->assignRole(Roles::ROOT);

        /** @var Configuration $configuration */
        $configuration = factory(Configuration::class)
            ->state('enabled')
            ->create([
                'name' => Config::CHECK_OUT,
            ]);

        $response = $this->actingAs($user)
            ->get(self::ROUTE);

        $response->assertOk()
            ->assertViewIs(self::VIEW)
            ->assertSeeText(trans('configurations.out'))
            ->assertSeeText($configuration->enabled_at->format('Y-m-d'));
    }
}
