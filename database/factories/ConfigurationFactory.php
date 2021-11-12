<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Constants\Modules;
use App\Models\Configuration;
use Faker\Generator as Faker;

$factory->define(Configuration::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'module' => $faker->randomElement(Modules::toArray())
    ];
});

$factory->state(Configuration::class, 'enabled', [
    'enabled_at' => now(),
]);
