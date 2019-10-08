<?php

use App\Role;
use Illuminate\Database\Seeder;

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
                'name' => 'root',
                'display_name' => 'Root',
                'description' => 'Root user'
            ],
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Administrator of a registered entity'
            ],
            [
                'name' => 'receptionist',
                'display_name' => 'Receptionist',
                'description' => 'Receptionist of the registered entity'
            ],
            [
                'name' => 'accountant',
                'display_name' => 'Public accountant',
                'description' => 'Public accountant'
            ],
            [
                'name' => 'manager',
                'display_name' => 'Manager',
                'description' => 'General manager'
            ],
        ];
    }
}
