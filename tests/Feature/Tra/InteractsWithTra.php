<?php

declare(strict_types=1);

namespace Tests\Feature\Tra;

use App\Constants\ComplianceScheme;
use App\Constants\IdentificationTypeCode;
use App\Constants\StayGuestRole;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\HotelComplianceProfile;
use App\Models\Room;
use App\Models\RoomOccupancy;
use App\Models\RoomType;
use App\Models\Stay;
use App\Models\StayGuest;

trait InteractsWithTra
{
    /**
     * @return array{hotel: Hotel, stay: Stay, occupancy: RoomOccupancy, principal: StayGuest, companion: StayGuest}
     */
    protected function traStay(): array
    {
        $hotel = Hotel::factory()->create(['country_code' => 'CO', 'timezone' => 'America/Bogota']);
        HotelComplianceProfile::factory()->for($hotel)->create([
            'jurisdiction' => 'CO',
            'scheme' => ComplianceScheme::Tra,
            'establishment_code' => '12345',
            'credentials' => ['secret' => 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6'],
            'enabled' => true,
        ]);

        $cc = $this->identificationType(IdentificationTypeCode::CitizenshipId);
        $ce = $this->identificationType(IdentificationTypeCode::ForeignerId);

        $juan = Guest::factory()->for($hotel)->create([
            'identification_type_id' => $cc->id,
            'first_name' => 'Juan',
            'second_first_name' => 'Carlos',
            'last_name' => 'Perez',
            'second_last_name' => 'Gomez',
            'identification_number' => '1020304050',
            'birth_date' => '1985-05-20',
            'gender' => 'M',
            'nationality' => 'COL',
        ]);

        $maria = Guest::factory()->for($hotel)->create([
            'identification_type_id' => $ce->id,
            'first_name' => 'Maria',
            'last_name' => 'Silva',
            'identification_number' => '987654321',
            'birth_date' => '1990-08-12',
            'gender' => 'F',
            'nationality' => 'BRA',
        ]);

        $roomType = RoomType::factory()->for($hotel)->create(['capacity' => 2]);
        $room = Room::factory()->for($hotel)->for($roomType)->create(['number' => '301']);

        $stay = Stay::factory()->for($hotel)->create([
            'responsible_guest_id' => $juan->id,
            'checked_in_at' => '2026-10-06 15:00:00',
            'expected_check_out_on' => '2026-10-10',
        ]);

        $occupancy = RoomOccupancy::factory()->for($stay)->for($room)->create([
            'principal_guest_id' => $juan->id,
            'nightly_rate' => '180000.00',
            'checked_in_at' => '2026-10-06 15:00:00',
            'expected_check_out_on' => '2026-10-10',
        ]);
        $occupancy->guests()->attach([$juan->id, $maria->id]);

        $principal = StayGuest::factory()->for($stay)->create([
            'guest_id' => $juan->id,
            'role' => StayGuestRole::Responsible,
            'checked_in_at' => '2026-10-06 15:00:00',
            'residence_country' => 'COL',
            'residence_subdivision' => '11',
            'residence_locality' => '11001',
            'origin_country' => 'COL',
            'origin_subdivision' => '05',
            'origin_locality' => '05001',
            'destination_country' => 'COL',
            'destination_subdivision' => '11',
            'destination_locality' => '11001',
            'travel_purpose' => '01',
            'transport_means' => '02',
        ]);

        $companion = StayGuest::factory()->for($stay)->create([
            'guest_id' => $maria->id,
            'role' => StayGuestRole::Companion,
            'checked_in_at' => '2026-10-06 15:00:00',
            'residence_country' => 'BRA',
            'origin_country' => 'BRA',
            'destination_country' => 'COL',
            'destination_subdivision' => '11',
            'destination_locality' => '11001',
            'travel_purpose' => '01',
            'transport_means' => '01',
        ]);

        return [
            'hotel' => $hotel,
            'stay' => $stay->refresh(),
            'occupancy' => $occupancy->refresh(),
            'principal' => $principal,
            'companion' => $companion,
        ];
    }
}
