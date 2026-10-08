<?php

declare(strict_types=1);

namespace App\Actions\Tra;

use App\Constants\TraSubmissionKind;
use App\Constants\TraSubmissionStatus;
use App\Models\Hotel;
use App\Models\RoomOccupancy;
use App\Models\Stay;
use App\Models\StayGuest;
use App\Services\Tra\TraPayloadMapper;
use Illuminate\Support\Str;

final class QueueTraSubmissions
{
    public function __construct(private TraPayloadMapper $mapper) {}

    public function execute(Hotel $hotel, Stay $stay): void
    {
        if ($hotel->country_code !== 'CO') {
            return;
        }

        $profile = $hotel->resolveCurrentComplianceProfile();

        if ($profile === null || ! $profile->enabled) {
            return;
        }

        $stay->loadMissing(['roomOccupancies.room', 'roomOccupancies.guests.identificationType', 'stayGuests.guest.identificationType']);

        foreach ($stay->roomOccupancies as $occupancy) {
            $this->queueOccupancy($hotel, $stay, $occupancy);
        }
    }

    private function queueOccupancy(Hotel $hotel, Stay $stay, RoomOccupancy $occupancy): void
    {
        $stayGuestsByGuestId = $stay->stayGuests->keyBy('guest_id');
        $principal = $occupancy->principal_guest_id !== null
            ? $stayGuestsByGuestId->get($occupancy->principal_guest_id)
            : null;

        if ($principal === null) {
            return;
        }

        $this->upsertSubmission($hotel, $stay, $occupancy, $principal, TraSubmissionKind::Principal);

        foreach ($occupancy->guests as $guest) {
            if ($guest->id === $principal->guest_id) {
                continue;
            }

            $companion = $stayGuestsByGuestId->get($guest->id);

            if ($companion !== null) {
                $this->upsertSubmission($hotel, $stay, $occupancy, $companion, TraSubmissionKind::Companion);
            }
        }
    }

    private function upsertSubmission(Hotel $hotel, Stay $stay, RoomOccupancy $occupancy, StayGuest $stayGuest, TraSubmissionKind $kind): void
    {
        $exists = $stay->traSubmissions()
            ->where('room_occupancy_id', $occupancy->id)
            ->where('stay_guest_id', $stayGuest->id)
            ->exists();

        if ($exists) {
            return;
        }

        $missing = $this->mapper->missingData($hotel, $stay, $occupancy, $stayGuest, $kind);

        $stay->traSubmissions()->create([
            'hotel_id' => $hotel->id,
            'room_occupancy_id' => $occupancy->id,
            'stay_guest_id' => $stayGuest->id,
            'kind' => $kind,
            'status' => $missing === []
                ? TraSubmissionStatus::Pending
                : TraSubmissionStatus::Incomplete,
            'idempotency_key' => (string) Str::uuid(),
            'payload_snapshot' => $missing === [] ? $this->mapper->map($hotel, $stay, $occupancy, $stayGuest, $kind) : null,
            'last_error_code' => $missing === [] ? null : 'incomplete_data',
            'last_error_message' => $missing === [] ? null : 'Faltan datos: ' . implode(', ', $missing),
        ]);
    }
}
