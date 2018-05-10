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

        $welkome = User::where('name', '=', 'Welkome')->first(['id', 'name']);
        $welkome->attachRole($root);;
    }
}
