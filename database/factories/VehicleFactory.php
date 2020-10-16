<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Vehicle::class, function (Faker $faker) {
    $faker->addProvider(new \Faker\Provider\ms_MY\Miscellaneous($faker));

    return [
        'registration' => $faker->jpjNumberPlate,
        'brand' => $faker->text(8),
        'color' => $faker->safeColorName,
        'vehicle_type_id' => function ()
        {
            return \App\Models\VehicleType::inRandomOrder()->first(['id'])->id;
        },
    ];
});
