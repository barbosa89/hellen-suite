<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Constants\FolioAdjustmentDirection;
use App\Constants\FolioAdjustmentType;
use App\Models\FolioAdjustment;
use App\Models\StayFolio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FolioAdjustment>
 */
class FolioAdjustmentFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'stay_folio_id' => StayFolio::factory(),
            'type' => FolioAdjustmentType::Courtesy,
            'direction' => FolioAdjustmentDirection::Credit,
            'amount_minor' => 25_000,
            'reason' => fake()->sentence(),
            'occurred_at' => now(),
            'idempotency_key' => fake()->uuid(),
            'folio_charge_id' => null,
            'recorded_by_user_id' => null,
        ];
    }
}
