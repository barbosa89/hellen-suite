<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Constants\Roles;
use Database\Seeders\RolesTableSeeder;
use Spatie\Permission\Models\Permission;
use Database\Seeders\PermissionsTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SyncUserPermissionsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesTableSeeder::class);
        $this->seed(PermissionsTableSeeder::class);
    }

    public function testSyncManagerUserPermissions()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $user->assignRole(Roles::MANAGER);

        $this->artisan('permissions:sync')
            ->assertExitCode(0);

        $user->load('permissions');

        $this->assertEquals(Permission::count(), $user->permissions->count());
    }

    public function testPermissionsAreNotDuplicated()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $user->assignRole(Roles::MANAGER);

        $user->syncPermissions(Permission::all(['id', 'name', 'guard_name']));

        $this->artisan('permissions:sync')
            ->assertExitCode(0);

        $user->load('permissions');

        $this->assertEquals(Permission::count(), $user->permissions->count());
    }
}
