<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Constants\CashMovementDirection;
use App\Constants\CashMovementType;
use App\Models\CashMovement;
use App\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CashMovement>
 */
class CashMovementFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'hotel_id' => Hotel::factory(),
            'type' => CashMovementType::ManualEntry,
            'direction' => CashMovementDirection::In,
            'amount_minor' => 50_000,
            'currency' => 'COP',
            'comment' => fake()->sentence(),
            'occurred_at' => now(),
            'idempotency_key' => fake()->uuid(),
            'payment_id' => null,
            'recorded_by_user_id' => null,
        ];
    }
}
