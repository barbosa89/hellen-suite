<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CashShift;
use App\Models\Hotel;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CashShift> */
class CashShiftFactory extends Factory
{
    public function definition(): array
    {
        return [
            'hotel_id' => Hotel::factory(),
            'number' => 1,
            'opened_at' => now(),
            'closed_at' => null,
            'opening_note' => null,
            'closing_note' => null,
        ];
    }
}
