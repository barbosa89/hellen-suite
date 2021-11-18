<?php

namespace Tests\Feature\Control\Configuration;

use App\User;
use Tests\TestCase;
use RolesTableSeeder;
use App\Constants\Roles;
use App\Models\Configuration;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConfigToggleTest extends TestCase
{
    use RefreshDatabase;

    private const ROUTE = 'configurations.toggle';

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesTableSeeder::class);
    }

    public function test_guest_user_is_redirected_to_login()
    {
        /** @var Configuration $configuration */
        $configuration = factory(Configuration::class)->create();

        $response = $this->put(route(self::ROUTE, $configuration->hash));

        $response->assertRedirect('/login');
    }

    public function test_user_cannot_access_without_root_role()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        /** @var Configuration $configuration */
        $configuration = factory(Configuration::class)->create();

        $response = $this->actingAs($user)
            ->put(route(self::ROUTE, $configuration->hash));

        $response->assertForbidden();
    }

    public function test_user_can_enable_config()
    {
        Carbon::setTestNow(now());

        /** @var User $user */
        $user = factory(User::class)->create();
        $user->assignRole(Roles::ROOT);

        /** @var Configuration $configuration */
        $configuration = factory(Configuration::class)->create([
            'enabled_at' => null,
        ]);

        $response = $this->actingAs($user)
            ->put(route(self::ROUTE, $configuration->hash));

        $response->assertRedirect('/configurations')
            ->assertSessionDoesntHaveErrors();

        $message = session('flash_notification')->first();

        $this->assertEquals(trans('common.updated.successfully'), $message->message);
        $this->assertEquals('success', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(false, $message->overlay);

        $this->assertDatabaseHas('configurations', [
            'id' => $configuration->id,
            'enabled_at' => now(),
        ]);
    }

    public function test_user_can_disable_config()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $user->assignRole(Roles::ROOT);

        /** @var Configuration $configuration */
        $configuration = factory(Configuration::class)
            ->state('enabled')
            ->create();

        $response = $this->actingAs($user)
            ->put(route(self::ROUTE, $configuration->hash));

        $response->assertRedirect('/configurations')
            ->assertSessionDoesntHaveErrors();

        $message = session('flash_notification')->first();

        $this->assertEquals(trans('common.updated.successfully'), $message->message);
        $this->assertEquals('success', $message->level);
        $this->assertEquals(false, $message->important);
        $this->assertEquals(false, $message->overlay);

        $this->assertDatabaseHas('configurations', [
            'id' => $configuration->id,
            'enabled_at' => null,
        ]);
    }
}
