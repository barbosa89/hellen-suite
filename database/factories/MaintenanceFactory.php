<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Asset;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaintenanceFactory extends Factory
{
    public function definition(): array
    {
        $file = UploadedFile::fake()->create("{$this->faker->word}.pdf", 10);

        $path = $file->storeAs('public', $file->hashName());

        return [
            'date' => now()->format('Y-m-d'),
            'commentary' => $this->faker->sentence(3),
            'value' => $this->faker->randomNumber(4),
            'invoice' => $path,
            'maintainable_id' => Asset::factory()->create(),
            'maintainable_type' => Asset::class,
            'user_id' => User::factory()->create()
        ];
    }
}
