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
        $this->call(RolesTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(VehicleTypesTableSeeder::class);
        $this->call(IdentificationTypesTableSeeder::class);
        $this->call(CountriesTableSeeder::class);
        $this->call(CurrencySeeder::class);
        $this->call(PlanSeeder::class);

        if (app()->environment('local')) {
            $this->call(UsersTableSeeder::class);
            $this->call(AssignmentsSeeder::class);
            $this->call(HotelTableSeeder::class);
            $this->call(GuestTableSeeder::class);
            $this->call(VehicleTableSeeder::class);
        }
    }
}
