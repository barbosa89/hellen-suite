<?php

declare(strict_types=1);

namespace Tests\Feature\Tra;

use App\Models\Guest;
use App\Models\Hotel;
use App\Models\HotelComplianceProfile;
use App\Models\IdentificationType;
use App\Models\Room;
use App\Models\RoomType;
use Database\Seeders\JurisdictionSubdivisionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TraCheckInValidationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_requires_a_principal_per_room_when_tra_is_enabled(): void
    {
        $this->seed(JurisdictionSubdivisionSeeder::class);
        [$hotel, $room, $payload] = $this->scenario();

        unset($payload['room_occupancies'][0]['principal_guest_key']);

        $this->post(route('hotels.stays.store', $hotel), $payload)
            ->assertSessionHasErrors('room_occupancies.0.principal_guest_key');
    }

    #[Test]
    public function it_requires_legal_and_travel_data_when_tra_is_enabled(): void
    {
        $this->seed(JurisdictionSubdivisionSeeder::class);
        [$hotel, $room, $payload] = $this->scenario();

        unset($payload['guests'][0]['birth_date'], $payload['guests'][0]['travel_purpose']);
        $payload['guests'][0]['origin_locality'] = '99999';

        $this->post(route('hotels.stays.store', $hotel), $payload)
            ->assertSessionHasErrors([
                'guests.0.birth_date',
                'guests.0.travel_purpose',
                'guests.0.origin_locality',
            ]);
    }

    #[Test]
    public function it_rejects_incomplete_existing_guest_profiles(): void
    {
        $this->seed(JurisdictionSubdivisionSeeder::class);
        [$hotel, $room, $payload] = $this->scenario();

        $guest = Guest::factory()->for($hotel)->create();
        $payload['guests'][0] = ['key' => 'responsible', 'guest_id' => $guest->id] + $this->travelSnapshot();
        $payload['room_occupancies'][0]['guest_keys'] = ['responsible'];
        unset($payload['guests'][1]);
        $payload['guests'] = array_values($payload['guests']);

        $this->post(route('hotels.stays.store', $hotel), $payload)
            ->assertSessionHasErrors('guests.0.guest_id');
    }

    /** @return array{Hotel, Room, array<string, mixed>} */
    private function scenario(): array
    {
        $hotel = Hotel::factory()->create(['country_code' => 'CO', 'timezone' => 'America/Bogota']);
        HotelComplianceProfile::factory()->for($hotel)->create(['enabled' => true]);

        $roomType = RoomType::factory()->for($hotel)->create(['capacity' => 2]);
        $room = Room::factory()->for($hotel)->for($roomType)->create(['number' => '301']);
        $identificationType = IdentificationType::factory()->create();

        $payload = [
            'expected_check_out_on' => now()->addDay()->toDateString(),
            'responsible_guest_key' => 'responsible',
            'guests' => [
                array_merge([
                    'key' => 'responsible',
                    'identification_type_id' => $identificationType->id,
                    'first_name' => 'Juan',
                    'last_name' => 'Perez',
                    'identification_number' => '1020304050',
                    'birth_date' => '1985-05-20',
                    'gender' => 'M',
                    'nationality' => 'COL',
                ], $this->travelSnapshot()),
                array_merge([
                    'key' => 'companion',
                    'identification_type_id' => $identificationType->id,
                    'first_name' => 'Ana',
                    'last_name' => 'Lopez',
                    'identification_number' => '900001',
                    'birth_date' => '1990-08-12',
                    'gender' => 'F',
                    'nationality' => 'COL',
                ], $this->travelSnapshot()),
            ],
            'room_occupancies' => [[
                'room_id' => $room->id,
                'nightly_rate' => '180000.00',
                'guest_keys' => ['responsible', 'companion'],
                'principal_guest_key' => 'responsible',
            ]],
        ];

        return [$hotel, $room, $payload];
    }

    /** @return array<string, string> */
    private function travelSnapshot(): array
    {
        return [
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
        ];
    }
}
