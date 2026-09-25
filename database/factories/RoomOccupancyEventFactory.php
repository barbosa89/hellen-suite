<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Constants\LodgingChargePolicy;
use App\Constants\RoomOccupancyEventType;
use App\Models\RoomOccupancy;
use App\Models\RoomOccupancyEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<RoomOccupancyEvent> */
class RoomOccupancyEventFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'room_occupancy_id' => RoomOccupancy::factory(),
            'user_id' => null,
            'type' => RoomOccupancyEventType::CheckedOut,
            'before_data' => ['checked_out_at' => null],
            'after_data' => [
                'checked_out_at' => now()->toDateTimeString(),
                'lodging_charge_policy' => LodgingChargePolicy::ConsumedNights->value,
            ],
        ];
    }
}
