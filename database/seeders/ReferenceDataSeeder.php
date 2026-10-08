<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Catalogs required for the application to work. Loaded by a data migration so
 * every installation gets them, including the packaged NativePHP database.
 */
class ReferenceDataSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            IdentificationTypeSeeder::class,
            JurisdictionSubdivisionSeeder::class,
        ]);
    }
}
