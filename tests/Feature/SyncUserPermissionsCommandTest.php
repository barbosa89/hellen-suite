<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Constants\Roles;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Console\Command;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SyncUserPermissionsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Role::create([
            'name' => Roles::MANAGER,
            'guard_name' => config('auth.defaults.guard')
        ]);

        Permission::create([
            'name' => 'rooms.index',
            'guard_name' => config('auth.defaults.guard')
        ]);
    }

    public function testSyncManagerUserPermissions()
    {
        /** @var User $user */
        $user = User::factory()->create();

        $user->assignRole(Roles::MANAGER);

        $this->artisan('permissions:sync')
            ->assertExitCode(Command::SUCCESS);

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
            ->assertExitCode(Command::SUCCESS);

        $user->load('permissions');

        $this->assertEquals(Permission::count(), $user->permissions->count());
    }
}
