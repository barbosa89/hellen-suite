<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class NoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'content' => $this->faker->sentence(20),
            'team_member_name' => $this->faker->name,
            'team_member_email' => $this->faker->email
        ];
    }
}
