<?php

declare(strict_types=1);

namespace App\Http\Requests\Concerns;

use App\Constants\Country;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\HotelComplianceProfile;
use App\Models\JurisdictionLocality;
use App\Models\JurisdictionSubdivision;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use League\ISO3166\ISO3166;

use function in_array;

trait HasTraGuestRules
{
    /**
     * @return array<string, mixed>
     */
    protected function guestIdentityRules(string $prefix): array
    {
        $key = fn (string $field): string => $prefix === '' ? $field : "{$prefix}.{$field}";

        return [
            $key('second_first_name') => ['nullable', 'string', 'max:100'],
            $key('second_last_name') => ['nullable', 'string', 'max:100'],
            $key('birth_date') => ['nullable', 'date', 'before:today'],
            $key('gender') => ['nullable', 'string', Rule::in(['M', 'F'])],
            $key('nationality') => ['nullable', 'string', 'size:3', Rule::in($this->isoAlpha3())],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function travelSnapshotRules(string $prefix): array
    {
        $key = fn (string $field): string => $prefix === '' ? $field : "{$prefix}.{$field}";

        return [
            $key('residence_country') => ['nullable', 'string', 'size:3', Rule::in($this->isoAlpha3())],
            $key('residence_subdivision') => ['nullable', 'string', 'max:16'],
            $key('residence_locality') => ['nullable', 'string', 'max:16'],
            $key('origin_country') => ['nullable', 'string', 'size:3', Rule::in($this->isoAlpha3())],
            $key('origin_subdivision') => ['nullable', 'string', 'max:16'],
            $key('origin_locality') => ['nullable', 'string', 'max:16'],
            $key('destination_country') => ['nullable', 'string', 'size:3', Rule::in($this->isoAlpha3())],
            $key('destination_subdivision') => ['nullable', 'string', 'max:16'],
            $key('destination_locality') => ['nullable', 'string', 'max:16'],
            $key('travel_purpose') => ['nullable', 'string', 'regex:/^[0-9]{2}$/'],
            $key('transport_means') => ['nullable', 'string', 'regex:/^[0-9]{2}$/'],
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $guests
     */
    protected function validateTraGuests(Validator $validator, Hotel $hotel, array $guests, string $path = 'guests', string $messages = 'stays.validation'): void
    {
        if (! HotelComplianceProfile::enabledFor($hotel)) {
            return;
        }

        $ids = collect($guests)->map(fn (array $guest): mixed => $guest['guest_id'] ?? null)->filter()->all();

        /** @var Collection<int, Guest> $stored */
        $stored = $hotel->guests()->whereKey($ids)->get()->keyBy('id');

        $subdivisions = JurisdictionSubdivision::query()->where('country_code', 'CO')->pluck('id', 'code');
        $localities = JurisdictionLocality::query()
            ->whereIn('subdivision_id', $subdivisions->values())
            ->get(['subdivision_id', 'code'])
            ->groupBy('subdivision_id')
            ->map(fn (Collection $rows): Collection => $rows->pluck('code'));

        foreach (array_values($guests) as $index => $entry) {
            $base = $path === '' ? '' : "{$path}.{$index}";
            $this->validateTraIdentity($validator, $base, $entry, $stored->get($entry['guest_id'] ?? 0), $messages);
            $this->validateTraTravel($validator, $base, $entry, $subdivisions, $localities, $messages);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $occupancies
     * @param  array<int, string>  $guestKeys
     */
    protected function validateTraPrincipals(Validator $validator, Hotel $hotel, array $occupancies, array $guestKeys, string $path, string $messages = 'stays.validation'): void
    {
        if (! HotelComplianceProfile::enabledFor($hotel)) {
            return;
        }

        foreach (array_values($occupancies) as $index => $occupancy) {
            $principal = $occupancy['principal_guest_key'] ?? null;

            if (blank($principal)) {
                $validator->errors()->add("{$path}.{$index}.principal_guest_key", trans("{$messages}.principal_required"));

                continue;
            }

            if (! in_array($principal, $guestKeys, true)) {
                $validator->errors()->add("{$path}.{$index}.principal_guest_key", trans("{$messages}.principal_invalid"));
            }
        }
    }

    /**
     * @param  array<string, mixed>  $entry
     */
    private function validateTraIdentity(Validator $validator, string $base, array $entry, null|Guest $guest, string $messages): void
    {
        if ($guest === null && isset($entry['guest_id'])) {
            return;
        }

        if ($guest === null) {
            foreach (['birth_date', 'gender', 'nationality'] as $field) {
                if (blank($entry[$field] ?? null)) {
                    $validator->errors()->add(self::fieldKey($base, $field), trans("{$messages}.tra_identity_required"));
                }
            }

            return;
        }

        if (blank($guest->birth_date) || blank($guest->gender) || blank($guest->nationality)) {
            $validator->errors()->add(self::fieldKey($base, 'guest_id'), trans("{$messages}.tra_guest_incomplete"));
        }
    }

    /**
     * @param  array<string, mixed>  $entry
     * @param  Collection<string, int>  $subdivisions
     * @param  Collection<int, Collection<int, string>>  $localities
     */
    private function validateTraTravel(Validator $validator, string $base, array $entry, Collection $subdivisions, Collection $localities, string $messages): void
    {
        foreach (['residence_country', 'origin_country', 'destination_country', 'travel_purpose', 'transport_means'] as $field) {
            if (blank($entry[$field] ?? null)) {
                $validator->errors()->add(self::fieldKey($base, $field), trans("{$messages}.tra_travel_required"));
            }
        }

        $this->validateGeoTriple($validator, $base, $entry, 'residence', $subdivisions, $localities, $messages);
        $this->validateGeoTriple($validator, $base, $entry, 'origin', $subdivisions, $localities, $messages);
        $this->validateGeoTriple($validator, $base, $entry, 'destination', $subdivisions, $localities, $messages);
    }

    /**
     * @param  array<string, mixed>  $entry
     * @param  Collection<string, int>  $subdivisions
     * @param  Collection<int, Collection<int, string>>  $localities
     */
    private function validateGeoTriple(Validator $validator, string $base, array $entry, string $kind, Collection $subdivisions, Collection $localities, string $messages): void
    {
        $country = $entry["{$kind}_country"] ?? null;
        $subdivision = $entry["{$kind}_subdivision"] ?? null;
        $locality = $entry["{$kind}_locality"] ?? null;

        if (blank($country)) {
            return;
        }

        if ($country !== Country::COL->value) {
            if (filled($subdivision) || filled($locality)) {
                $validator->errors()->add(self::fieldKey($base, "{$kind}_subdivision"), trans("{$messages}.tra_geo_foreign"));
            }

            return;
        }

        if (blank($subdivision) || blank($locality)) {
            $validator->errors()->add(self::fieldKey($base, "{$kind}_subdivision"), trans("{$messages}.tra_geo_required"));

            return;
        }

        $subdivisionId = $subdivisions->get($subdivision);

        if ($subdivisionId === null || ! ($localities->get($subdivisionId) ?? collect())->contains($locality)) {
            $validator->errors()->add(self::fieldKey($base, "{$kind}_locality"), trans("{$messages}.tra_geo_invalid"));
        }
    }

    private static function fieldKey(string $base, string $field): string
    {
        return $base === '' ? $field : "{$base}.{$field}";
    }

    /**
     * @return list<string>
     */
    private function isoAlpha3(): array
    {
        return array_column((new ISO3166())->all(), 'alpha3');
    }
}
