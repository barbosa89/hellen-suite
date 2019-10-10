<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->getRoles() as $rol) {
            Role::create($rol);
        }
    }

    /**
     * Default roles in application
     *
     * @return array
     */
    public function getRoles()
    {
        return [
            [
                'name' => 'root'
            ],
            [
                'name' => 'manager'
            ],
            [
                'name' => 'admin'
            ],
            [
                'name' => 'receptionist'
            ],
            [
                'name' => 'accountant'
            ],
        ];
    }
}
