<?php

use App\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('email', 'manager@welkome.com')->first(['id']);

        factory(Vehicle::class, 8)->create([
            'user_id' => $user->id
        ]);
    }
}
