<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Hotel;
use App\Models\Stay;
use App\Models\StayFolio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StayFolio>
 */
class StayFolioFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'hotel_id' => Hotel::factory(),
            'stay_id' => fn (array $attributes): Stay => Stay::factory()->create(['hotel_id' => $attributes['hotel_id']]),
            'label' => 'Room ' . fake()->numberBetween(100, 999),
            'currency' => 'COP',
            'closed_at' => null,
            'closed_by_user_id' => null,
        ];
    }
}
