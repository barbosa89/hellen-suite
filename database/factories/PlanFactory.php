<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Plan;
use Faker\Generator as Faker;

$factory->define(Plan::class, function (Faker $faker) {
    $types = [Plan::FREE, Plan::BASIC, Plan::PREMIUM, Plan::PARTNER];

    return [
        'description' => $faker->text(8),
        'type' => $types[$faker->numberBetween(0, 3)],
        'status' => true,
        'ends_at' => now()->addYear()
    ];
});
