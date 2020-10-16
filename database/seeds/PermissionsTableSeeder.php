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
        foreach (config('settings.modules') as $module) {
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

        // Shift permissions
        Permission::insert([
            [
                'name' => 'shifts.index',
                'guard_name' => config('auth.defaults.guard')
            ],
            [
                'name' => 'shifts.create',
                'guard_name' => config('auth.defaults.guard')
            ],
            [
                'name' => 'shifts.close',
                'guard_name' => config('auth.defaults.guard')
            ],
            [
                'name' => 'shifts.show',
                'guard_name' => config('auth.defaults.guard')
            ],
        ]);

        Permission::create([
            'name' => 'payments.close',
            'guard_name' => config('auth.defaults.guard')
        ]);

        Permission::create([
            'name' => 'vouchers.close',
            'guard_name' => config('auth.defaults.guard')
        ]);

        Permission::create([
            'name' => 'vouchers.open',
            'guard_name' => config('auth.defaults.guard')
        ]);

        ### Transaction permissions ###

        Permission::create([
            'name' => 'transactions.sale',
            'guard_name' => config('auth.defaults.guard')
        ]);

        Permission::create([
            'name' => 'transactions.entry',
            'guard_name' => config('auth.defaults.guard')
        ]);

        Permission::create([
            'name' => 'transactions.loss',
            'guard_name' => config('auth.defaults.guard')
        ]);

        Permission::create([
            'name' => 'transactions.discard',
            'guard_name' => config('auth.defaults.guard')
        ]);

        // Products

        // Permissions to do vouchers
        Permission::create([
            'name' => 'products.vouchers',
            'guard_name' => config('auth.defaults.guard')
        ]);

        // Dining service

        // Only for dining service
        Permission::create([
            'name' => 'dining.sale',
            'guard_name' => config('auth.defaults.guard')
        ]);

        // Props

        // Permissions to do vouchers
        Permission::create([
            'name' => 'props.vouchers',
            'guard_name' => config('auth.defaults.guard')
        ]);

        // Notes permissions
        Permission::insert([
            [
                'name' => 'notes.index',
                'guard_name' => config('auth.defaults.guard')
            ],
            [
                'name' => 'notes.create',
                'guard_name' => config('auth.defaults.guard')
            ],
            [
                'name' => 'notes.show',
                'guard_name' => config('auth.defaults.guard')
            ],
        ]);
    }
}
