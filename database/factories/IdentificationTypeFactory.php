<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Constants\IdentificationTypeCode;
use App\Models\IdentificationType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IdentificationType>
 */
class IdentificationTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => IdentificationTypeCode::Passport,
        ];
    }
}
