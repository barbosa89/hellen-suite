<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Hotel;
use App\Models\Payment;
use App\Models\PaymentVoucher;
use App\Models\StayFolio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaymentVoucher>
 */
class PaymentVoucherFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'number' => fake()->unique()->numberBetween(1, 999_999),
            'hotel_id' => Hotel::factory(),
            'payment_id' => fn (array $attributes): Payment => Payment::factory()->for(
                StayFolio::factory()->create(['hotel_id' => $attributes['hotel_id']]),
                'folio',
            )->create(),
        ];
    }
}
