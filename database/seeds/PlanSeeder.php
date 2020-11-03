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
            'months' => 1,
            'type' => Plan::FREE,
            'status' => true
        ]);

        Plan::create([
            'price' => 365000,
            'months' => 12,
            'type' => Plan::BASIC,
            'status' => true
        ]);

        Plan::create([
            'price' => 700000,
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
