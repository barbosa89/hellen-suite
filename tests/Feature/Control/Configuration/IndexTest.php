<?php

namespace Tests\Feature\Control\Configuration;

use App\User;
use Tests\TestCase;
use RolesTableSeeder;
use App\Constants\Roles;
use App\Constants\Config;
use App\Constants\Modules;
use App\Models\Configuration;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
        $configuration = factory(Configuration::class)
            ->state('enabled')
            ->create([
                'name' => Config::CHECK_OUT,
            ]);

        $response = $this->actingAs($user)
            ->get(self::ROUTE);

        $response->assertOk()
            ->assertViewIs(self::VIEW)
            ->assertViewHas('configurations', function ($data) use ($configuration) {
                /** @var Configuration $item */
                $item = $data->first();

                return $item->id === $configuration->id
                    && $item->isEnabled();
            })
            ->assertSeeText(Config::trans($configuration->name))
            ->assertSeeText(Modules::trans($configuration->module))
            ->assertSeeText(now()->format('Y-m-d'));
    }
}
