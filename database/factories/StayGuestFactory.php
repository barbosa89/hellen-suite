<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Constants\StayGuestRole;
use App\Models\Guest;
use App\Models\Stay;
use App\Models\StayGuest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StayGuest>
 */
class StayGuestFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'stay_id' => Stay::factory(),
            'guest_id' => Guest::factory(),
            'role' => StayGuestRole::Companion,
        ];
    }
}
