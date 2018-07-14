<?php

use App\Welkome\VehicleType;
use Illuminate\Database\Seeder;

class VehicleTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        VehicleType::create(['type' => 'car']);
        VehicleType::create(['type' => 'truck']);
        VehicleType::create(['type' => 'van']);
        VehicleType::create(['type' => 'bus']);
        VehicleType::create(['type' => 'minibus']);
        VehicleType::create(['type' => 'bicycle']);
        VehicleType::create(['type' => 'motorcycle']);
        VehicleType::create(['type' => 'trailer']);
        VehicleType::create(['type' => 'skateboard']);
        VehicleType::create(['type' => 'tricycle']);
        VehicleType::create(['type' => 'campers']);
        VehicleType::create(['type' => 'minivan']);        
    }
}
