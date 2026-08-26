<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Guest;
use App\Models\Hotel;
use App\Models\IdentificationType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Guest>
 */
class GuestFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hotel_id' => Hotel::factory(),
            'identification_type_id' => fn (): IdentificationType => IdentificationType::query()->first()
                ?? IdentificationType::factory()->create(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'identification_number' => fake()->unique()->numerify('##########'),
            'mobile' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
        ];
    }
}
