<?php

namespace Database\Seeders;

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
        Plan::insert([
            [
                'price' => 0,
                'months' => 1,
                'type' => Plan::FREE,
                'status' => true
            ],
            [
                'price' => 365000,
                'months' => 12,
                'type' => Plan::BASIC,
                'status' => true
            ],
            [
                'price' => 700000,
                'months' => 12,
                'type' => Plan::PREMIUM,
                'status' => false
            ],
            [
                'price' => 0,
                'months' => 12,
                'type' => Plan::SPONSOR,
                'status' => true
            ]
        ]);
    }
}
