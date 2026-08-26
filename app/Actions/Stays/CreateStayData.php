<?php

declare(strict_types=1);

namespace App\Actions\Stays;

use Carbon\CarbonImmutable;

final readonly class CreateStayData
{
    /**
     * @param  array<int, GuestEntryData>  $guests
     * @param  array<int, RoomOccupancyData>  $roomOccupancies
     */
    public function __construct(
        public CarbonImmutable $expectedCheckOutOn,
        public string $responsibleGuestKey,
        public array $guests,
        public array $roomOccupancies,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromValidated(array $data): self
    {
        return new self(
            expectedCheckOutOn: CarbonImmutable::parse($data['expected_check_out_on']),
            responsibleGuestKey: $data['responsible_guest_key'],
            guests: array_map(GuestEntryData::fromValidated(...), $data['guests']),
            roomOccupancies: array_map(RoomOccupancyData::fromValidated(...), $data['room_occupancies']),
        );
    }
}
