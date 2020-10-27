<?php

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Plan::create([
            'price' => 0,
            'months' => 2,
            'type' => Plan::FREE,
            'status' => true
        ]);

        Plan::create([
            'price' => 180000,
            'months' => 12,
            'type' => Plan::BASIC,
            'status' => true
        ]);

        Plan::create([
            'price' => 500000,
            'months' => 12,
            'type' => Plan::PREMIUM,
            'status' => false
        ]);

        Plan::create([
            'price' => 0,
            'months' => 12,
            'type' => Plan::SPONSOR,
            'status' => true
        ]);
    }
}
