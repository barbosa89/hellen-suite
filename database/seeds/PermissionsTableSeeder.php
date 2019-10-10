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
        foreach ($this->getModules() as $module) {
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
    }

    /**
     * List of modules name
     *
     * @return array
     */
    public function getModules()
    {
        return [
            'users',
            'members',
            'assets',
            'companies',
            'guests',
            'hotels',
            'identification_types',
            'invoices',
            'payments',
            'products',
            'rooms',
            'services',
            'shifts',
            'subscriptions',
            'vehicles',
        ];
    }
}
