<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(AssignmentsSeeder::class);
        $this->call(VehicleTypesTableSeeder::class);
        $this->call(IdentificationTypesTableSeeder::class);
        $this->call(CountriesTableSeeder::class);
    }
}
