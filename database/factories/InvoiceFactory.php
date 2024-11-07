<?php

namespace Database\Factories;

use App\Models\Currency;
use App\Models\IdentificationType;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
            'identification_type_id' => IdentificationType::inRandomOrder()->first(),
            'currency_id' => Currency::where('code', Currency::COP)->first(),
        ];
    }
}
