<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\JurisdictionLocality;
use App\Models\JurisdictionSubdivision;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JurisdictionLocality>
 */
class JurisdictionLocalityFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subdivision_id' => JurisdictionSubdivision::factory(),
            'code' => fake()->unique()->numerify('#####'),
            'name' => fake()->unique()->city(),
            'type' => 'Municipio',
            'is_active' => true,
        ];
    }
}
