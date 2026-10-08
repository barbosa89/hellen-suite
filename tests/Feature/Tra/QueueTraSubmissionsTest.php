<?php

declare(strict_types=1);

namespace Tests\Feature\Tra;

use App\Constants\IdentificationTypeCode;
use App\Constants\TraSubmissionKind;
use App\Constants\TraSubmissionStatus;
use App\Jobs\SendTraSubmission;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\HotelComplianceProfile;
use App\Models\IdentificationType;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Stay;
use Database\Seeders\JurisdictionSubdivisionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QueueTraSubmissionsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_queues_principal_and_companion_submissions_on_check_in(): void
    {
        $this->seed(JurisdictionSubdivisionSeeder::class);
        Queue::fake();

        [$hotel, $room] = $this->traHotelWithRoom();

        $this->post(route('hotels.stays.store', $hotel), $this->payload($hotel, $room))
            ->assertRedirect();

        $stay = Stay::query()->sole();

        $this->assertSame(2, $stay->traSubmissions()->count());
        $this->assertSame(1, $stay->traSubmissions()->where('kind', TraSubmissionKind::Principal)->where('status', TraSubmissionStatus::Pending)->count());
        $this->assertSame(1, $stay->traSubmissions()->where('kind', TraSubmissionKind::Companion)->where('status', TraSubmissionStatus::Pending)->count());

        $principal = $stay->traSubmissions()->where('kind', TraSubmissionKind::Principal)->sole();
        $this->assertSame('301', $principal->payload_snapshot['numero_habitacion']);
        $this->assertSame(180000, $principal->payload_snapshot['tarifa_habitacion']);
        $this->assertSame(1, $principal->payload_snapshot['numero_acompanantes']);
        $this->assertSame('1020304050', $stay->traSubmissions()->where('kind', TraSubmissionKind::Companion)->sole()->payload_snapshot['documento_principal']);

        Queue::assertPushed(SendTraSubmission::class, 2);
    }

    #[Test]
    public function it_queues_nothing_without_an_enabled_profile(): void
    {
        Queue::fake();

        $hotel = Hotel::factory()->create(['country_code' => 'CO']);
        $room = $this->room($hotel);
        $guest = Guest::factory()->for($hotel)->create();

        $this->post(route('hotels.stays.store', $hotel), [
            'expected_check_out_on' => now()->addDay()->toDateString(),
            'responsible_guest_key' => 'responsible',
            'guests' => [['key' => 'responsible', 'guest_id' => $guest->id]],
            'room_occupancies' => [[
                'room_id' => $room->id,
                'nightly_rate' => '150000.00',
                'guest_keys' => ['responsible'],
            ]],
        ])->assertRedirect();

        $this->assertSame(0, Stay::query()->sole()->traSubmissions()->count());
        Queue::assertNothingPushed();
    }

    #[Test]
    public function it_marks_unsupported_document_types_as_incomplete(): void
    {
        $this->seed(JurisdictionSubdivisionSeeder::class);
        Queue::fake();

        [$hotel, $room] = $this->traHotelWithRoom();
        $dni = IdentificationType::factory()->create(['code' => IdentificationTypeCode::ForeignNationalId]);

        $payload = $this->payload($hotel, $room);
        $payload['guests'][0]['identification_type_id'] = $dni->id;

        $this->post(route('hotels.stays.store', $hotel), $payload)->assertRedirect();

        $submission = Stay::query()->sole()->traSubmissions()->where('kind', TraSubmissionKind::Principal)->sole();
        $this->assertSame(TraSubmissionStatus::Incomplete, $submission->status);
        $this->assertSame('incomplete_data', $submission->last_error_code);
    }

    /** @return array{Hotel, Room} */
    private function traHotelWithRoom(): array
    {
        $hotel = Hotel::factory()->create(['country_code' => 'CO', 'timezone' => 'America/Bogota']);
        HotelComplianceProfile::factory()->for($hotel)->create([
            'establishment_code' => '12345',
            'credentials' => ['secret' => 'token'],
            'enabled' => true,
        ]);

        return [$hotel, $this->room($hotel)];
    }

    private function room(Hotel $hotel): Room
    {
        $roomType = RoomType::factory()->for($hotel)->create(['capacity' => 2]);

        return Room::factory()->for($hotel)->for($roomType)->create(['number' => '301']);
    }

    /** @return array<string, mixed> */
    private function payload(Hotel $hotel, Room $room): array
    {
        $identificationType = IdentificationType::factory()->create();

        $travel = [
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

        return [
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
                ], $travel),
                array_merge([
                    'key' => 'companion',
                    'identification_type_id' => $identificationType->id,
                    'first_name' => 'Ana',
                    'last_name' => 'Lopez',
                    'identification_number' => '900001',
                    'birth_date' => '1990-08-12',
                    'gender' => 'F',
                    'nationality' => 'COL',
                ], $travel),
            ],
            'room_occupancies' => [[
                'room_id' => $room->id,
                'nightly_rate' => '180000.00',
                'guest_keys' => ['responsible', 'companion'],
                'principal_guest_key' => 'responsible',
            ]],
        ];
    }
}
