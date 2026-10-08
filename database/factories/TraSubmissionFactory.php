<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Constants\TraSubmissionKind;
use App\Constants\TraSubmissionStatus;
use App\Models\Hotel;
use App\Models\RoomOccupancy;
use App\Models\Stay;
use App\Models\StayGuest;
use App\Models\TraSubmission;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TraSubmission>
 */
class TraSubmissionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hotel_id' => Hotel::factory(),
            'stay_id' => Stay::factory(),
            'room_occupancy_id' => RoomOccupancy::factory(),
            'stay_guest_id' => StayGuest::factory(),
            'kind' => TraSubmissionKind::Principal,
            'status' => TraSubmissionStatus::Pending,
            'idempotency_key' => (string) Str::uuid(),
            'payload_snapshot' => null,
        ];
    }
}
