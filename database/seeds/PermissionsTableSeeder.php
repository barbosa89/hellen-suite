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
            'name' => 'invoices.payment.close',
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

        // Permission::create([
        //     'name' => 'rooms.pool',
        //     'guard_name' => config('auth.defaults.guard')
        // ]);

        // Permission::create([
        //     'name' => 'rooms.assign',
        //     'guard_name' => config('auth.defaults.guard')
        // ]);
    }
}
