<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Plan;
use Faker\Generator as Faker;

$factory->define(Plan::class, function (Faker $faker) {
    $types = [Plan::FREE, Plan::BASIC, Plan::PREMIUM, Plan::SPONSOR];

    return [
        'price' => $faker->numberBetween(180000, 500000),
        'months' => $faker->numberBetween(2, 12),
        'type' => $types[$faker->numberBetween(0, 3)],
        'status' => true,
    ];
});
