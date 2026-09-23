<?php

declare(strict_types=1);

namespace App\Data\Reservations;

final readonly class ReservationGuestData
{
    public function __construct(
        public string $key,
        public null|int $guestId,
        public null|int $identificationTypeId,
        public null|string $firstName,
        public null|string $lastName,
        public null|string $identificationNumber,
        public null|string $mobile,
        public null|string $email,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromValidated(array $data): self
    {
        return new self(
            key: $data['key'],
            guestId: $data['guest_id'] ?? null,
            identificationTypeId: $data['identification_type_id'] ?? null,
            firstName: $data['first_name'] ?? null,
            lastName: $data['last_name'] ?? null,
            identificationNumber: $data['identification_number'] ?? null,
            mobile: $data['mobile'] ?? null,
            email: $data['email'] ?? null,
        );
    }
}
