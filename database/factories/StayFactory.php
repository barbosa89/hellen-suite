<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Constants\StayStatus;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\Stay;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Stay>
 */
class StayFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hotel_id' => Hotel::factory(),
            'responsible_guest_id' => fn (array $attributes): Guest => Guest::factory()->create([
                'hotel_id' => $attributes['hotel_id'],
            ]),
            'status' => StayStatus::Active,
            'currency' => 'COP',
            'checked_in_at' => now(),
            'expected_check_out_on' => now()->addDay()->toDateString(),
            'checked_out_at' => null,
        ];
    }
}
