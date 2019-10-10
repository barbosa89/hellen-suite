<?php

use App\User;
use Illuminate\Database\Seeder;
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
        $root = Role::where('name', '=', 'root')->first(['id', 'name']);
        $manager = Role::where('name', '=', 'manager')->first(['id', 'name']);
        $receptionist = Role::where('name', '=', 'receptionist')->first(['id', 'name']);

        $welkome = User::where('name', '=', 'Welkome')->first(['id', 'name']);
        $welkome->assignRole($root->name);

        $managerUser = User::where('name', '=', 'Manager')->first(['id', 'name']);
        $managerUser->assignRole($manager->name);

        $recepUser = User::where('name', '=', 'Recep')->first(['id', 'name']);
        $recepUser->assignRole($receptionist->name);
    }
}
