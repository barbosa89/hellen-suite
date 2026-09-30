<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Constants\FolioChargeType;
use App\Models\FolioCharge;
use App\Models\StayFolio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FolioCharge>
 */
class FolioChargeFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'stay_folio_id' => StayFolio::factory(),
            'type' => FolioChargeType::Manual,
            'description' => fake()->words(3, true),
            'quantity' => 1,
            'unit_amount_minor' => 50_000,
            'total_amount_minor' => 50_000,
            'service_start_on' => today(),
            'service_end_on' => today()->addDay(),
            'posted_at' => now(),
            'idempotency_key' => fake()->uuid(),
            'room_occupancy_id' => null,
            'recorded_by_user_id' => null,
        ];
    }
}
