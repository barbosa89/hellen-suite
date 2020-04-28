<?php

use Faker\Generator as Faker;

$factory->define(App\Welkome\Vehicle::class, function (Faker $faker) {
    $faker->addProvider(new \Faker\Provider\ms_MY\Miscellaneous($faker));

    return [
        'registration' => $faker->jpjNumberPlate,
        'brand' => $faker->text(8),
        'color' => $faker->safeColorName,
        'vehicle_type_id' => function ()
        {
            return \App\Welkome\VehicleType::inRandomOrder()->first(['id'])->id;
        },
    ];
});
