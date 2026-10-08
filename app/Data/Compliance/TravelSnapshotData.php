<?php

declare(strict_types=1);

namespace App\Data\Compliance;

use function is_string;

final readonly class TravelSnapshotData
{
    public function __construct(
        public null|string $residenceCountry,
        public null|string $residenceSubdivision,
        public null|string $residenceLocality,
        public null|string $originCountry,
        public null|string $originSubdivision,
        public null|string $originLocality,
        public null|string $destinationCountry,
        public null|string $destinationSubdivision,
        public null|string $destinationLocality,
        public null|string $travelPurpose,
        public null|string $transportMeans,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromGuestEntry(array $data): self
    {
        return new self(
            residenceCountry: self::code($data['residence_country'] ?? null, 3),
            residenceSubdivision: self::code($data['residence_subdivision'] ?? null),
            residenceLocality: self::code($data['residence_locality'] ?? null),
            originCountry: self::code($data['origin_country'] ?? null, 3),
            originSubdivision: self::code($data['origin_subdivision'] ?? null),
            originLocality: self::code($data['origin_locality'] ?? null),
            destinationCountry: self::code($data['destination_country'] ?? null, 3),
            destinationSubdivision: self::code($data['destination_subdivision'] ?? null),
            destinationLocality: self::code($data['destination_locality'] ?? null),
            travelPurpose: self::code($data['travel_purpose'] ?? null, 2),
            transportMeans: self::code($data['transport_means'] ?? null, 2),
        );
    }

    /** @return array<string, string|null> */
    public function toAttributes(): array
    {
        return [
            'residence_country' => $this->residenceCountry,
            'residence_subdivision' => $this->residenceSubdivision,
            'residence_locality' => $this->residenceLocality,
            'origin_country' => $this->originCountry,
            'origin_subdivision' => $this->originSubdivision,
            'origin_locality' => $this->originLocality,
            'destination_country' => $this->destinationCountry,
            'destination_subdivision' => $this->destinationSubdivision,
            'destination_locality' => $this->destinationLocality,
            'travel_purpose' => $this->travelPurpose,
            'transport_means' => $this->transportMeans,
        ];
    }

    private static function code(mixed $value, null|int $length = null): null|string
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        $code = mb_strtoupper(trim($value));

        return $length === null ? $code : mb_substr($code, 0, $length);
    }
}
