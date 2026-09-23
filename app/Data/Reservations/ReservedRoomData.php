<?php

declare(strict_types=1);

namespace App\Data\Reservations;

final readonly class ReservedRoomData
{
    /** @param array<int, string> $guestKeys */
    public function __construct(
        public int $roomId,
        public string $nightlyRate,
        public array $guestKeys,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromValidated(array $data): self
    {
        return new self(
            roomId: (int) $data['room_id'],
            nightlyRate: (string) $data['nightly_rate'],
            guestKeys: $data['guest_keys'],
        );
    }
}
