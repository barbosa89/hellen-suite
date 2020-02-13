<?php

use App\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
class AssignmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Roles list
        $root = Role::where('name', 'root')->first(['id', 'name', 'guard_name']);
        $manager = Role::where('name', 'manager')->first(['id', 'name', 'guard_name']);
        $admin = Role::where('name', 'admin')->first(['id', 'name', 'guard_name']);
        $receptionist = Role::where('name', 'receptionist')->first(['id', 'name', 'guard_name']);
        $accountant = Role::where('name', 'accountant')->first(['id', 'name', 'guard_name']);

        // Root user
        $welkome = User::where('name', '=', 'Welkome')->first(['id', 'name']);
        $welkome->assignRole($root);

        // Testing users
        $managerUser = User::where('name', '=', 'Manager')->first(['id', 'name']);
        $managerUser->assignRole($manager);

        $adminUser = User::where('name', '=', 'Admin')->first(['id', 'name']);
        $adminUser->assignRole($admin);

        $accountantUser = User::where('name', '=', 'Accountant')->first(['id', 'name']);
        $accountantUser->assignRole($accountant);

        $recepUser = User::where('name', '=', 'Recep')->first(['id', 'name']);
        $recepUser->assignRole($receptionist);

        $tachira = User::where('name', '=', 'Táchiras')->first(['id', 'name']);
        $tachira->assignRole($manager);

        // Assign permissions to roles
        $permissions = Permission::all(['id', 'name', 'guard_name']);
        $managerUser->syncPermissions($permissions);

        $tachira->syncPermissions($permissions);
    }
}
