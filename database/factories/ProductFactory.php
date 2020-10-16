<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Product::class, function (Faker $faker) {
    $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));

    return [
        'description' => $faker->productName,
        'price' => $faker->randomNumber(4),
        'quantity' => 20
    ];
});
