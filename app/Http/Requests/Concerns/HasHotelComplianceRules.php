<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns;

use App\Models\Hotel;
use App\Models\HotelComplianceProfile;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use League\ISO3166\ISO3166;

use function is_string;

trait HasHotelComplianceRules
{
    /**
     * @return array<string, mixed>
     */
    protected function complianceRules(null|Hotel $ignoreHotel = null): array
    {
        $codeUnique = Rule::unique('hotel_compliance_profiles', 'establishment_code');

        if ($ignoreHotel instanceof Hotel && $this->currentComplianceProfile($ignoreHotel) !== null) {
            $codeUnique->ignore($this->currentComplianceProfile($ignoreHotel));
        }

        return [
            'country_code' => ['nullable', 'string', 'size:2', Rule::in($this->isoAlpha2())],
            'timezone' => ['nullable', 'string', 'max:64', Rule::in(timezone_identifiers_list())],
            'establishment_code' => ['nullable', 'string', 'max:100', $codeUnique],
            'credential' => ['nullable', 'string', 'max:500'],
            'compliance_enabled' => ['nullable', 'boolean'],
        ];
    }

    protected function normalizeCompliance(): void
    {
        $merge = [];

        if ($this->exists('country_code')) {
            $country = $this->input('country_code');
            $merge['country_code'] = is_string($country) && trim($country) !== ''
                ? mb_strtoupper(trim($country))
                : null;
        }

        if ($this->exists('timezone')) {
            $timezone = $this->input('timezone');
            $merge['timezone'] = is_string($timezone) && trim($timezone) !== ''
                ? trim($timezone)
                : null;
        }

        if ($this->exists('establishment_code')) {
            $code = $this->input('establishment_code');
            $merge['establishment_code'] = is_string($code) && trim($code) !== ''
                ? trim($code)
                : null;
        }

        if ($merge !== []) {
            $this->merge($merge);
        }
    }

    protected function validateCompliance(Validator $validator, null|Hotel $existingHotel = null): void
    {
        $validator->after(function (Validator $validator) use ($existingHotel): void {
            if (! $this->boolean('compliance_enabled')) {
                return;
            }

            $country = $this->input('country_code') ?? $existingHotel?->country_code;

            if ($country !== 'CO') {
                $validator->errors()->add('compliance_enabled', trans('hotels.validation.compliance_requires_co'));

                return;
            }

            $storedCode = $this->currentComplianceProfile($existingHotel)?->establishment_code;

            if (blank($this->input('establishment_code')) && blank($storedCode)) {
                $validator->errors()->add('establishment_code', trans('hotels.validation.establishment_code_required'));
            }

            $hasNewCredential = filled($this->input('credential'));
            $hasStoredCredential = $this->currentComplianceProfile($existingHotel)?->isConfigured() ?? false;

            if (! $hasNewCredential && ! $hasStoredCredential) {
                $validator->errors()->add('credential', trans('hotels.validation.credential_required'));
            }
        });
    }

    private function currentComplianceProfile(null|Hotel $hotel): null|HotelComplianceProfile
    {
        if (! $hotel instanceof Hotel) {
            return null;
        }

        $country = $this->input('country_code') ?? $hotel->country_code;

        if (blank($country)) {
            return null;
        }

        return $hotel->complianceProfiles()
            ->where('jurisdiction', $country)
            ->where('scheme', HotelComplianceProfile::schemeForJurisdiction($country))
            ->first();
    }

    /**
     * @return list<string>
     */
    private function isoAlpha2(): array
    {
        return array_column((new ISO3166())->all(), 'alpha2');
    }
}
