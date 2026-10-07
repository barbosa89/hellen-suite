<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\JurisdictionSubdivision;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JurisdictionSubdivision>
 */
class JurisdictionSubdivisionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'country_code' => 'CO',
            'code' => fake()->unique()->numerify('##'),
            'name' => fake()->unique()->state(),
            'type' => 'department',
            'iso_reference' => null,
            'source' => 'test',
            'version' => 'test',
            'is_active' => true,
        ];
    }
}
