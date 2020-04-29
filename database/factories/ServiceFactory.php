<?php

use Faker\Generator as Faker;

$factory->define(App\Welkome\Service::class, function (Faker $faker) {
    $faker->addProvider(new \Bezhanov\Faker\Provider\Food($faker));

    return [
        'description' => $faker->ingredient,
        'price' => $faker->randomNumber(4),
        'is_dining_service' => ceil($faker->numberBetween(0,1))
    ];
});
