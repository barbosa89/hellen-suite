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
        $admin = Role::where('name', '=', 'admin')->first(['id', 'name']);
        $receptionist = Role::where('name', '=', 'receptionist')->first(['id', 'name']);

        $welkome = User::where('name', '=', 'Welkome')->first(['id', 'name']);
        $welkome->attachRole($root);

        $adminUser = User::where('name', '=', 'Admin')->first(['id', 'name']);
        $adminUser->attachRole($admin);

        $recepUser = User::where('name', '=', 'Recep')->first(['id', 'name']);
        $recepUser->attachRole($receptionist);
    }
}
