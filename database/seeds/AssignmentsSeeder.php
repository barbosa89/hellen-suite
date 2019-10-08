<?php

use App\Role;
use App\User;
use Illuminate\Database\Seeder;

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
        $welkome->attachRole($root);

        $managerUser = User::where('name', '=', 'Manager')->first(['id', 'name']);
        $managerUser->attachRole($manager);

        $recepUser = User::where('name', '=', 'Recep')->first(['id', 'name']);
        $recepUser->attachRole($receptionist);
    }
}
