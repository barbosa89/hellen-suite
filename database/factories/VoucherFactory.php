<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Hotel;
use App\Models\Voucher;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoucherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $value = $this->faker->randomNumber(5);

        return [
            'number' => Str::random(8),
            'origin' => $this->faker->city,
            'destination' => $this->faker->city,
            'subvalue' => $value,
            'value' => $value,
            'type' => Voucher::TYPES[$this->faker->numberBetween(0, 5)],
            'hotel_id' => function () {
                return Hotel::factory()->create()->id;
            },
            'user_id' => function () {
                return User::factory()->create()->id;
            },
        ];
    }
}
