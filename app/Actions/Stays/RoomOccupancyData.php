<?php

declare(strict_types=1);

namespace App\Actions\Stays;

final readonly class RoomOccupancyData
{
    /** @param array<int, string> $guestKeys */
    public function __construct(
        public int $roomId,
        public string $nightlyRate,
        public array $guestKeys,
        public null|string $principalGuestKey,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromValidated(array $data): self
    {
        return new self(
            roomId: (int) $data['room_id'],
            nightlyRate: $data['nightly_rate'],
            guestKeys: $data['guest_keys'],
            principalGuestKey: $data['principal_guest_key'] ?? null,
        );
    }
}
