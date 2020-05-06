<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Welkome\Note;
use Faker\Generator as Faker;

$factory->define(Note::class, function (Faker $faker) {
    return [
        'content' => $faker->sentence(20),
        'team_member_name' => $faker->name,
        'team_member_email' => $faker->email
    ];
});
