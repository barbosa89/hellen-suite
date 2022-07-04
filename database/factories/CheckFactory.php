<?php

namespace Database\Factories;

use App\Models\Guest;
use App\Models\Voucher;
use Illuminate\Database\Eloquent\Factories\Factory;

class CheckFactory extends Factory
{
    public function definition(): array
    {
        return [
            'in_at' => now(),
            'out_at' => now(),
            'guest_id' => function () {
                return Guest::factory()->create()->id;
            },
            'voucher_id' => function () {
                return Voucher::factory()->create()->id;
            },
        ];
    }
}
