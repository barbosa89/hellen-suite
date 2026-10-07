<?php

declare(strict_types=1);

namespace App\Actions\Hotels;

use App\Models\Hotel;
use App\Models\HotelComplianceProfile;

use function array_key_exists;
use function count;

final class SyncHotelComplianceProfile
{
    /**
     * @param  array{establishment_code?: string|null, credential?: string|null, compliance_enabled?: bool|null}  $data
     */
    public function execute(Hotel $hotel, array $data): void
    {
        if (blank($hotel->country_code)) {
            $hotel->complianceProfiles()->delete();

            return;
        }

        $jurisdiction = $hotel->country_code;
        $scheme = HotelComplianceProfile::schemeForJurisdiction($jurisdiction);

        $hotel->complianceProfiles()
            ->where(fn ($query) => $query
                ->where('jurisdiction', '!=', $jurisdiction)
                ->orWhere('scheme', '!=', $scheme))
            ->delete();

        $hasComplianceKeys = count(array_intersect_key($data, [
            'establishment_code' => true,
            'credential' => true,
            'compliance_enabled' => true,
        ])) > 0;

        /** @var HotelComplianceProfile $profile */
        $profile = $hotel->complianceProfiles()->firstOrNew([
            'jurisdiction' => $jurisdiction,
            'scheme' => $scheme,
        ]);

        if (! $profile->exists && ! $hasComplianceKeys) {
            return;
        }

        if (array_key_exists('establishment_code', $data)) {
            $profile->establishment_code = $data['establishment_code'];
        }

        if (filled($data['credential'] ?? null)) {
            $profile->credentials = ['secret' => $data['credential']];
        }

        if (array_key_exists('compliance_enabled', $data)) {
            $profile->enabled = (bool) $data['compliance_enabled'];
        }

        if (! $this->shouldPersistProfile($profile)) {
            return;
        }

        $profile->save();
    }

    protected function shouldPersistProfile(HotelComplianceProfile $profile): bool
    {
        return $profile->exists
            || $profile->enabled
            || filled($profile->establishment_code)
            || $profile->isConfigured();
    }
}
