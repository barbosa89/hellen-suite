<?php

namespace Tests\Traits;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

trait HasPermissions
{
    public function createPermission(string $name): Permission
    {
        return Permission::findOrCreate($name, config('auth.defaults.guard'));
    }

    /**
     * @param  array<int, string>  $names
     */
    public function createPermissions(array $names): void
    {
        foreach ($names as $name) {
            $this->createPermission($name);
        }
    }

    public function createRole(string $name): void
    {
        Role::create([
            'name' => $name,
            'guard_name' => config('auth.defaults.guard'),
        ]);
    }

    /**
     * @param  array<int, string>  $names
     */
    public function createRoles(array $names): void
    {
        foreach ($names as $name) {
            $this->createRole($name);
        }
    }
}
