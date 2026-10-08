<?php

declare(strict_types=1);

namespace App\Services\Tra;

use App\Constants\Country;
use App\Constants\IdentificationTypeCode;
use App\Constants\TraSubmissionKind;
use App\Models\Hotel;
use App\Models\RoomOccupancy;
use App\Models\Stay;
use App\Models\StayGuest;
use Carbon\CarbonImmutable;

/**
 * Maps PMS domain data to the TRA API payload (§3.1 principal, §3.2 companions).
 *
 * Document-type mapping is pending homologation against the official TRA manual:
 * CC/CE/TI/RC map directly and PASSPORT follows the Colombian PAS convention,
 * while NATIONAL_ID has no confirmed equivalent and is reported as missing data.
 */
final class TraPayloadMapper
{
    private const DOCUMENT_MAP = [
        'CC' => 'CC',
        'CE' => 'CE',
        'TI' => 'TI',
        'RC' => 'RC',
        'PASSPORT' => 'PAS',
    ];

    /** @return array<string, mixed> */
    public function map(Hotel $hotel, Stay $stay, RoomOccupancy $occupancy, StayGuest $stayGuest, TraSubmissionKind $kind): array
    {
        $profile = $hotel->resolveCurrentComplianceProfile();
        $guest = $stayGuest->guest;
        $principalDoc = $this->principalDocument($stay, $occupancy);

        $payload = [
            'rnt' => $profile?->establishment_code,
            'token' => $profile?->credentials['secret'] ?? null,
            'tipo_documento' => $this->documentCode($guest->identificationType->code),
            'numero_documento' => $guest->identification_number,
            'primer_nombre' => $this->upper($guest->first_name),
            'primer_apellido' => $this->upper($guest->last_name),
            'fecha_nacimiento' => $guest->birth_date?->toDateString(),
            'genero' => $guest->gender,
            'nacionalidad' => $this->upper($guest->nationality),
            'fecha_entrada' => $this->stayDate($hotel->timezone, $stayGuest->checked_in_at ?? $occupancy->checked_in_at),
            'fecha_salida' => $this->stayDate($hotel->timezone, $stay->expected_check_out_on, true),
            'numero_habitacion' => $occupancy->room->number,
        ];

        if ($guest->second_first_name !== null) {
            $payload['segundo_nombre'] = $this->upper($guest->second_first_name);
        }

        if ($guest->second_last_name !== null) {
            $payload['segundo_apellido'] = $this->upper($guest->second_last_name);
        }

        $payload += $this->geoPayload($stayGuest, 'residence');
        $payload += $this->geoPayload($stayGuest, 'origin');
        $payload += $this->geoPayload($stayGuest, 'destination');

        $payload['motivo_viaje'] = $stayGuest->travel_purpose;
        $payload['medio_transporte'] = $stayGuest->transport_means;

        if ($kind === TraSubmissionKind::Principal) {
            $payload['tarifa_habitacion'] = (int) round((float) $occupancy->nightly_rate);
            $payload['numero_acompanantes'] = max(0, $occupancy->guests->count() - 1);
        } else {
            $payload['documento_principal'] = $principalDoc;
        }

        return $payload;
    }

    /** @return list<string> */
    public function missingData(Hotel $hotel, Stay $stay, RoomOccupancy $occupancy, StayGuest $stayGuest, TraSubmissionKind $kind): array
    {
        $missing = [];
        $profile = $hotel->resolveCurrentComplianceProfile();

        if ($profile === null || blank($profile->establishment_code) || blank($profile->credentials['secret'] ?? null)) {
            $missing[] = 'credenciales TRA del hotel';
        }

        $guest = $stayGuest->guest;

        foreach ([
            'tipo de documento' => $this->documentCode($guest->identificationType->code),
            'número de documento' => $guest->identification_number,
            'primer nombre' => $guest->first_name,
            'primer apellido' => $guest->last_name,
            'fecha de nacimiento' => $guest->birth_date?->toDateString(),
            'género' => $guest->gender,
            'nacionalidad' => $guest->nationality,
        ] as $label => $value) {
            if (blank($value)) {
                $missing[] = $label;
            }
        }

        foreach (['residence' => 'residencia', 'origin' => 'procedencia', 'destination' => 'destino'] as $field => $label) {
            $country = $stayGuest->{"{$field}_country"};

            if (blank($country)) {
                $missing[] = "país de {$label}";

                continue;
            }

            if ($country === Country::COL->value && (blank($stayGuest->{"{$field}_subdivision"}) || blank($stayGuest->{"{$field}_locality"}))) {
                $missing[] = "departamento/municipio de {$label}";
            }
        }

        if (blank($stayGuest->travel_purpose)) {
            $missing[] = 'motivo de viaje';
        }

        if (blank($stayGuest->transport_means)) {
            $missing[] = 'medio de transporte';
        }

        if ($occupancy->room === null) {
            $missing[] = 'habitación';
        }

        if ($kind === TraSubmissionKind::Companion && blank($this->principalDocument($stay, $occupancy))) {
            $missing[] = 'documento del huésped principal';
        }

        return array_values(array_unique($missing));
    }

    public function documentCode(IdentificationTypeCode $code): null|string
    {
        return self::DOCUMENT_MAP[$code->value] ?? null;
    }

    private function principalDocument(Stay $stay, RoomOccupancy $occupancy): null|string
    {
        if ($occupancy->principal_guest_id === null) {
            return null;
        }

        $principal = $stay->stayGuests->firstWhere('guest_id', $occupancy->principal_guest_id);

        return $principal?->guest->identification_number;
    }

    /** @return array<string, string|null> */
    private function geoPayload(StayGuest $stayGuest, string $kind): array
    {
        $country = $stayGuest->{"{$kind}_country"};

        $payload = ["pais_{$this->spanishKind($kind)}" => $this->upper($country)];

        if ($country === Country::COL->value) {
            $payload["departamento_{$this->spanishKind($kind)}"] = $stayGuest->{"{$kind}_subdivision"};
            $payload["municipio_{$this->spanishKind($kind)}"] = $stayGuest->{"{$kind}_locality"};
        }

        return $payload;
    }

    private function spanishKind(string $kind): string
    {
        return match ($kind) {
            'residence' => 'residencia',
            'origin' => 'procedencia',
            default => 'destino',
        };
    }

    private function upper(null|string $value): null|string
    {
        return $value === null ? null : mb_strtoupper($value);
    }

    private function stayDate(null|string $timezone, mixed $value, bool $dateOnly = false): null|string
    {
        if ($value === null) {
            return null;
        }

        $date = $value instanceof CarbonImmutable ? $value : CarbonImmutable::parse($value);

        if ($dateOnly || $timezone === null) {
            return $date->toDateString();
        }

        return $date->setTimezone($timezone)->toDateString();
    }
}
