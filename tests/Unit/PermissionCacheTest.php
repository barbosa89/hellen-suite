<?php

namespace Tests\Unit;

use App\User;
use Tests\TestCase;
use RolesTableSeeder;
use PermissionsTableSeeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PermissionCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_permission_cache_is_stored_on_login()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret',
        ]);

        $response->assertRedirect('/home')
            ->assertSessionDoesntHaveErrors();

        $this->assertTrue(Cache::has('user-' . $user->id));
    }

    public function test_permission_cache_is_forget_on_logout()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        Cache::add('user-' . $user->id, [], 60);

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/')
            ->assertSessionDoesntHaveErrors();

        $this->assertTrue(!Cache::has('user-' . $user->id));
    }

    public function test_authenticated_user_can_get_permissions_from_helper()
    {
        $this->seed(RolesTableSeeder::class);
        $this->seed(PermissionsTableSeeder::class);

        /** @var User $user */
        $user = factory(User::class)->create();
        $user->assignRole('manager');
        $user->givePermissionTo('shifts.index');

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret',
        ]);

        $response->assertRedirect('/home')
            ->assertSessionDoesntHaveErrors();

        $this->assertEquals(['shifts.index'], get_user_permissions());
    }

    public function test_guest_user_cannot_get_permissions_from_helper()
    {
        $this->assertEmpty(get_user_permissions());
    }
}
