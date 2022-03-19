<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (config('settings.modules') as $module) {
            Permission::findOrCreate(
                $module . '.index',
                config('auth.defaults.guard')
            );

            Permission::findOrCreate(
                $module . '.create',
                config('auth.defaults.guard')
            );

            Permission::findOrCreate(
                $module . '.edit',
                config('auth.defaults.guard')
            );

            Permission::findOrCreate(
                $module . '.destroy',
                config('auth.defaults.guard')
            );

            Permission::findOrCreate(
                $module . '.show',
                config('auth.defaults.guard')
            );
        }

        // Shift permissions
        Permission::findOrCreate(
            'shifts.index',
            config('auth.defaults.guard')
        );

        Permission::findOrCreate(
            'shifts.index',
            config('auth.defaults.guard')
        );

        Permission::findOrCreate(
            'shifts.create',
            config('auth.defaults.guard')
        );

        Permission::findOrCreate(
            'shifts.close',
            config('auth.defaults.guard')
        );

        Permission::findOrCreate(
            'shifts.show',
            config('auth.defaults.guard')
        );

        // Payments
        Permission::findOrCreate(
            'payments.close',
            config('auth.defaults.guard')
        );

        // Vouchers
        Permission::findOrCreate(
            'vouchers.close',
            config('auth.defaults.guard')
        );

        Permission::findOrCreate(
            'vouchers.open',
            config('auth.defaults.guard')
        );

        // Transactions
        Permission::findOrCreate(
            'transactions.sale',
            config('auth.defaults.guard')
        );

        Permission::findOrCreate(
            'transactions.entry',
            config('auth.defaults.guard')
        );

        Permission::findOrCreate(
            'transactions.loss',
            config('auth.defaults.guard')
        );

        Permission::findOrCreate(
            'transactions.discard',
            config('auth.defaults.guard')
        );

        // Products
        // Permissions to do vouchers
        Permission::findOrCreate(
            'products.vouchers',
            config('auth.defaults.guard')
        );

        // Dining service
        // Only for dining service
        Permission::findOrCreate(
            'dining.sale',
            config('auth.defaults.guard')
        );

        // Props
        // Permissions to do vouchers
        Permission::findOrCreate(
            'props.vouchers',
            config('auth.defaults.guard')
        );

        // Notes permissions
        Permission::findOrCreate(
            'notes.index',
            config('auth.defaults.guard')
        );

        Permission::findOrCreate(
            'notes.create',
            config('auth.defaults.guard')
        );

        Permission::findOrCreate(
            'notes.show',
            config('auth.defaults.guard')
        );

        // Rooms
        Permission::findOrCreate(
            'rooms.toggle',
            config('auth.defaults.guard')
        );
    }
}
