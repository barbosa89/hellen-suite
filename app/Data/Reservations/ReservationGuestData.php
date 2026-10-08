<?php

declare(strict_types=1);

namespace App\Data\Reservations;

use App\Data\Compliance\TravelSnapshotData;

final readonly class ReservationGuestData
{
    public function __construct(
        public string $key,
        public null|int $guestId,
        public null|int $identificationTypeId,
        public null|string $firstName,
        public null|string $secondFirstName,
        public null|string $lastName,
        public null|string $secondLastName,
        public null|string $identificationNumber,
        public null|string $birthDate,
        public null|string $gender,
        public null|string $nationality,
        public null|string $mobile,
        public null|string $email,
        public TravelSnapshotData $travel,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromValidated(array $data): self
    {
        return new self(
            key: $data['key'],
            guestId: $data['guest_id'] ?? null,
            identificationTypeId: $data['identification_type_id'] ?? null,
            firstName: $data['first_name'] ?? null,
            secondFirstName: $data['second_first_name'] ?? null,
            lastName: $data['last_name'] ?? null,
            secondLastName: $data['second_last_name'] ?? null,
            identificationNumber: $data['identification_number'] ?? null,
            birthDate: $data['birth_date'] ?? null,
            gender: $data['gender'] ?? null,
            nationality: isset($data['nationality']) ? mb_strtoupper($data['nationality']) : null,
            mobile: $data['mobile'] ?? null,
            email: $data['email'] ?? null,
            travel: TravelSnapshotData::fromGuestEntry($data),
        );
    }

    /** @return array<string, string|null> */
    public function identityAttributes(): array
    {
        return [
            'second_first_name' => $this->secondFirstName,
            'second_last_name' => $this->secondLastName,
            'birth_date' => $this->birthDate,
            'gender' => $this->gender,
            'nationality' => $this->nationality,
        ];
    }
}
