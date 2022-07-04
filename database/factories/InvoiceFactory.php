<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Currency;
use Illuminate\Support\Str;
use App\Models\IdentificationType;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        $value = $this->faker->numberBetween(100000, 200000);

        return [
            'number' => Str::random(12),
            'customer_name' => $this->faker->name,
            'customer_dni' => $this->faker->randomNumber(7),
            'value' => $value,
            'total' => $value,
            'status' => Invoice::PENDING,
            'identification_type_id' => function () {
                return IdentificationType::inRandomOrder()->first()->id;
            },
            'currency_id' => function () {
                return Currency::where('code', Currency::COP)->first()->id;
            }
        ];
    }
}
