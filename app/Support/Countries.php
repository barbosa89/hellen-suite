<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\JurisdictionSubdivision;
use League\ISO3166\ISO3166;

final class Countries
{
    /**
     * @return list<array{code: string, name: string}>
     */
    public static function alpha3(): array
    {
        return collect((new ISO3166())->all())
            ->map(fn (array $country): array => ['code' => $country['alpha3'], 'name' => $country['name']])
            ->sortBy('name')
            ->values()
            ->all();
    }

    /**
     * @return list<array{code: string, name: string, type: string, iso_reference: string|null}>
     */
    public static function subdivisions(string $countryCode): array
    {
        return JurisdictionSubdivision::query()
            ->forCountry($countryCode)
            ->active()
            ->orderBy('name')
            ->get(['code', 'name', 'type', 'iso_reference'])
            ->all();
    }
}
