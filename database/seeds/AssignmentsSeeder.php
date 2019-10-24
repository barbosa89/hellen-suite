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
        // Companies - reception
        // $permissions_companies_recep = Permission::where('name', 'companies.index')
        //     ->orwhere('name', 'companies.create')
        //     ->orwhere('name', 'companies.show')
        //     ->orwhere('name', 'companies.edit')
        //     ->get(['id', 'name', 'guard_name']);

        // $receptionist->syncPermissions($permissions_companies_recep);

        // // Guests - reception
        // $permissions_guests_recep = Permission::where('name', 'guests.index')
        //     ->orwhere('name', 'guests.create')
        //     ->orwhere('name', 'guests.show')
        //     ->orwhere('name', 'guests.edit')
        //     ->get(['id', 'name', 'guard_name']);

        // $receptionist->syncPermissions($permissions_guests_recep);

        // // Invoices - reception
        // $permissions_invoices_recep = Permission::where('name', 'invoices.index')
        //     ->orwhere('name', 'invoices.create')
        //     ->orwhere('name', 'invoices.show')
        //     ->get(['id', 'name', 'guard_name']);

        // $receptionist->syncPermissions($permissions_invoices_recep);

        // // Payments - reception
        // $permissions_payments_recep = Permission::where('name', 'payments.index')
        //     ->orwhere('name', 'payments.create')
        //     ->get(['id', 'name', 'guard_name']);

        // $receptionist->syncPermissions($permissions_payments_recep);

        // // Products - reception
        // $permissions_products_recep = Permission::where('name', 'products.index')
        //     ->get(['id', 'name', 'guard_name']);

        // $receptionist->syncPermissions($permissions_products_recep);

        // // Rooms - reception
        // $permissions_rooms_recep = Permission::where('name', 'rooms.index')
        //     ->get(['id', 'name', 'guard_name']);

        // $receptionist->syncPermissions($permissions_rooms_recep);

        // // Shifts - reception
        // $permissions_shifts_recep = Permission::where('name', 'shifts.index')
        //     ->orwhere('name', 'shifts.create')
        //     ->orwhere('name', 'shifts.show')
        //     ->orwhere('name', 'shifts.edit')
        //     ->get(['id', 'name', 'guard_name']);

        // $receptionist->syncPermissions($permissions_shifts_recep);

        // // Vehicles - reception
        // $permissions_vehicles_recep = Permission::where('name', 'vehicles.index')
        //     ->orwhere('name', 'vehicles.create')
        //     ->orwhere('name', 'vehicles.show')
        //     ->orwhere('name', 'vehicles.edit')
        //     ->get(['id', 'name', 'guard_name']);

        // $receptionist->syncPermissions($permissions_vehicles_recep);
    }
}
