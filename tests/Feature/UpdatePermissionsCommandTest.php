<?php

namespace Tests\Feature;

use Tests\TestCase;
use PermissionsTableSeeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdatePermissionsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_app_admin_can_run_command_to_update_permissions()
    {
        $this->artisan('permissions:update')
            ->expectsOutput('The permissions were updated successfully')
            ->assertExitCode(0);
    }

    public function test_app_admin_can_update_permissions_without_duplicate_them()
    {
        $this->seed(PermissionsTableSeeder::class);

        $expected = Permission::count();

        $this->artisan('permissions:update')
            ->expectsOutput('The permissions were updated successfully')
            ->assertExitCode(0);

        $this->assertEquals($expected, Permission::count());
    }
}
