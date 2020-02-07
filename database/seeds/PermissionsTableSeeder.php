<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (config('welkome.modules') as $module) {
            Permission::insert([
                [
                    'name' => $module . '.index',
                    'guard_name' => config('auth.defaults.guard')
                ],
                [
                    'name' => $module . '.create',
                    'guard_name' => config('auth.defaults.guard')
                ],
                [
                    'name' => $module . '.edit',
                    'guard_name' => config('auth.defaults.guard')
                ],
                [
                    'name' => $module . '.destroy',
                    'guard_name' => config('auth.defaults.guard')
                ],
                [
                    'name' => $module . '.show',
                    'guard_name' => config('auth.defaults.guard')
                ],
            ]);
        }

        Permission::create([
            'name' => 'payments.close',
            'guard_name' => config('auth.defaults.guard')
        ]);

        Permission::create([
            'name' => 'invoices.close',
            'guard_name' => config('auth.defaults.guard')
        ]);

        Permission::create([
            'name' => 'invoices.open',
            'guard_name' => config('auth.defaults.guard')
        ]);

        Permission::create([
            'name' => 'invoices.losses',
            'guard_name' => config('auth.defaults.guard')
        ]);

        ### Transaction permissions ###

        // Products

        // CRUD Permissions to do transactions
        Permission::create([
            'name' => 'products.transactions',
            'guard_name' => config('auth.defaults.guard')
        ]);

        // Only for products sales
        Permission::create([
            'name' => 'products.sales',
            'guard_name' => config('auth.defaults.guard')
        ]);

        // Dining service

        // Only for dining service
        Permission::create([
            'name' => 'dining.sales',
            'guard_name' => config('auth.defaults.guard')
        ]);

        // Props

        // CRUD Permissions to do transactions
        Permission::create([
            'name' => 'props.transactions',
            'guard_name' => config('auth.defaults.guard')
        ]);
    }
}
