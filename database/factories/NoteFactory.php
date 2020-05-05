<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Welkome\Note;
use Faker\Generator as Faker;

$factory->define(Note::class, function (Faker $faker) {
    return [
        'content' => $faker->sentence(20)
    ];
});
