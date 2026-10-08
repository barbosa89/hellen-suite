<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Constants\ComplianceScheme;
use App\Models\Hotel;
use App\Models\HotelComplianceProfile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<HotelComplianceProfile>
 */
class HotelComplianceProfileFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hotel_id' => Hotel::factory(),
            'jurisdiction' => 'CO',
            'scheme' => ComplianceScheme::Tra,
            'establishment_code' => fake()->unique()->numerify('#####'),
            'credentials' => ['secret' => Str::random(32)],
            'enabled' => true,
        ];
    }
}
