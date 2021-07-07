<?php

namespace Tests\Feature;

use App\Constants\Roles;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PermissionsTableSeeder;
use RolesTableSeeder;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

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
        $user = factory(User::class)->create();

        $user->assignRole(Roles::MANAGER);

        $this->artisan('permissions:sync')
            ->assertExitCode(0);

        $user->load('permissions');

        $this->assertEquals(Permission::count(), $user->permissions->count());
    }
}
