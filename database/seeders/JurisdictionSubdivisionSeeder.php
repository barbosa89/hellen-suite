<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\JurisdictionLocality;
use App\Models\JurisdictionSubdivision;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class JurisdictionSubdivisionSeeder extends Seeder
{
    public function run(): void
    {
        /** @var array{source: string, subdivisions: list<array<string, mixed>>, localities: list<array<string, mixed>>} $data */
        $data = json_decode(File::get(database_path('seeders/data/divipola_co.json')), true);

        DB::transaction(function () use ($data): void {
            JurisdictionSubdivision::query()->upsert(
                array_map(fn (array $subdivision): array => [
                    'country_code' => $subdivision['country_code'],
                    'code' => $subdivision['code'],
                    'name' => $subdivision['name'],
                    'type' => $subdivision['type'],
                    'iso_reference' => $subdivision['iso_reference'],
                    'source' => $data['source'],
                    'version' => $data['retrieved_at'],
                    'is_active' => true,
                ], $data['subdivisions']),
                ['country_code', 'code'],
                ['name', 'type', 'iso_reference', 'source', 'version', 'is_active'],
            );

            $subdivisionIds = JurisdictionSubdivision::query()
                ->where('country_code', 'CO')
                ->pluck('id', 'code');

            JurisdictionLocality::query()->upsert(
                array_map(fn (array $locality): array => [
                    'subdivision_id' => $subdivisionIds[$locality['subdivision_code']],
                    'code' => $locality['code'],
                    'name' => $locality['name'],
                    'type' => $locality['type'],
                    'is_active' => true,
                ], $data['localities']),
                ['subdivision_id', 'code'],
                ['name', 'type', 'is_active'],
            );
        });
    }
}
