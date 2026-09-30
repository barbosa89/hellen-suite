<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Constants\PaymentMethod;
use App\Constants\PaymentType;
use App\Models\Payment;
use App\Models\StayFolio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'stay_folio_id' => StayFolio::factory(),
            'type' => PaymentType::Receipt,
            'method' => PaymentMethod::Cash,
            'amount_minor' => 50_000,
            'currency' => 'COP',
            'comment' => null,
            'support_path' => null,
            'paid_at' => now(),
            'idempotency_key' => fake()->uuid(),
            'parent_payment_id' => null,
            'recorded_by_user_id' => null,
        ];
    }
}
