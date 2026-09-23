<?php

declare(strict_types=1);

namespace App\Data\Reservations;

use Carbon\CarbonImmutable;

final readonly class ReservationData
{
    /**
     * @param  array<int, ReservationGuestData>  $guests
     * @param  array<int, ReservedRoomData>  $reservedRooms
     */
    public function __construct(
        public CarbonImmutable $plannedCheckInOn,
        public CarbonImmutable $plannedCheckOutOn,
        public string $responsibleGuestKey,
        public array $guests,
        public array $reservedRooms,
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromValidated(array $data): self
    {
        return new self(
            plannedCheckInOn: CarbonImmutable::parse($data['planned_check_in_on']),
            plannedCheckOutOn: CarbonImmutable::parse($data['planned_check_out_on']),
            responsibleGuestKey: $data['responsible_guest_key'],
            guests: array_map(ReservationGuestData::fromValidated(...), $data['guests']),
            reservedRooms: array_map(ReservedRoomData::fromValidated(...), $data['reserved_rooms']),
        );
    }
}
