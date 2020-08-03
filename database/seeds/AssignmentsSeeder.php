<?php

use App\Helpers\Permissions;
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
        // Root user
        $welkome = User::where('name', 'Welkome')->first(['id', 'name']);
        $welkome->assignRole('root');

        // Testing users
        $managerUser = User::where('name', 'Manager')->first(['id', 'name']);
        $managerUser->assignRole('manager');

        $adminUser = User::where('name', 'Admin')->first(['id', 'name']);
        $adminUser->assignRole('admin');

        $accountantUser = User::where('name', 'Accountant')->first(['id', 'name']);
        $accountantUser->assignRole('accountant');

        $recepUser = User::where('name', 'Receptionist')->first(['id', 'name']);
        $recepUser->assignRole('receptionist');

        $cashierUser = User::where('name', 'Cashier')->first(['id', 'name']);
        $cashierUser->assignRole('cashier');

        // Assign permissions to users
        $allPermissions = Permission::all(['id', 'name', 'guard_name']);
        $managerUser->syncPermissions($allPermissions);

        $adminUser->syncPermissions(Permissions::list('admin'));

        $accountantUser->syncPermissions(Permissions::list('accountant'));

        $recepUser->syncPermissions(Permissions::list('receptionist'));

        $cashierUser->syncPermissions(Permissions::list('cashier'));
    }
}
