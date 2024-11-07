<?php

namespace Database\Factories;

use App\Models\InvoicePayment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class InvoicePaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'number' => Str::random(12),
            'value' => $this->faker->randomNumber(6),
            'payment_method' => $this->faker->word,
            'status' => InvoicePayment::APPROVED,
        ];
    }
}
