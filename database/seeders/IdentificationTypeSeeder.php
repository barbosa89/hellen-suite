<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Constants\IdentificationTypeCode;
use App\Models\IdentificationType;
use Illuminate\Database\Seeder;

class IdentificationTypeSeeder extends Seeder
{
    public function run(): void
    {
        foreach (IdentificationTypeCode::cases() as $identificationTypeCode) {
            IdentificationType::query()->firstOrCreate([
                'code' => $identificationTypeCode->value,
            ]);
        }
    }
}
