<?php

declare(strict_types=1);

namespace Tests\Feature\Stays;

use App\Constants\HousekeepingStatus;
use App\Constants\StayGuestRole;
use App\Constants\StayStatus;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\IdentificationType;
use App\Models\Room;
use App\Models\RoomOccupancy;
use App\Models\RoomType;
use App\Models\Stay;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StayStoreTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_checks_in_a_group_with_existing_and_new_guests(): void
    {
        $hotel = Hotel::factory()->create();
        $identificationType = IdentificationType::factory()->create();
        $responsible = Guest::factory()->for($hotel)->create(['identification_type_id' => $identificationType->id]);
        [$firstRoom, $secondRoom] = $this->rooms($hotel);

        $this->post(route('hotels.stays.store', $hotel), [
            'expected_check_out_on' => now()->addDay()->toDateString(),
            'responsible_guest_key' => 'responsible',
            'guests' => [
                ['key' => 'responsible', 'guest_id' => $responsible->id],
                [
                    'key' => 'companion',
                    'identification_type_id' => $identificationType->id,
                    'first_name' => 'Ana',
                    'last_name' => 'López',
                    'identification_number' => '900001',
                    'mobile' => '3001234567',
                    'email' => 'ana@example.com',
                ],
            ],
            'room_occupancies' => [
                ['room_id' => $firstRoom->id, 'nightly_rate' => '150000.00', 'guest_keys' => ['responsible']],
                ['room_id' => $secondRoom->id, 'nightly_rate' => '175000.00', 'guest_keys' => ['companion']],
            ],
        ])->assertRedirect();

        $stay = Stay::query()->with(['stayGuests', 'roomOccupancies.guests'])->sole();
        $this->assertSame(StayStatus::Active, $stay->status);
        $this->assertTrue($stay->responsibleGuest->is($responsible));
        $this->assertCount(2, $stay->stayGuests);
        $this->assertTrue($stay->stayGuests->contains('role', StayGuestRole::Responsible));
        $this->assertCount(2, $stay->roomOccupancies);
        $this->assertSame('150000.00', $stay->roomOccupancies->first()->nightly_rate);
        $this->assertCount(1, $stay->roomOccupancies->first()->guests);
        $this->assertNotNull($stay->roomOccupancies->first()->guests->first()->pivot->created_at);
    }

    #[Test]
    public function it_rejects_a_dirty_or_occupied_room(): void
    {
        $hotel = Hotel::factory()->create();
        $identificationType = IdentificationType::factory()->create();
        $guest = Guest::factory()->for($hotel)->create(['identification_type_id' => $identificationType->id]);
        [$room] = $this->rooms($hotel);
        $room->update(['housekeeping_status' => HousekeepingStatus::Dirty]);

        $this->post(route('hotels.stays.store', $hotel), $this->payload($guest, $room))
            ->assertSessionHasErrors('room_occupancies.0.room_id');
    }

    #[Test]
    public function it_rejects_an_assignment_over_room_capacity(): void
    {
        $hotel = Hotel::factory()->create();
        $identificationType = IdentificationType::factory()->create();
        $responsible = Guest::factory()->for($hotel)->create(['identification_type_id' => $identificationType->id]);
        $roomType = RoomType::factory()->for($hotel)->create(['capacity' => 1]);
        $room = Room::factory()->for($hotel)->for($roomType)->create();

        $payload = $this->payload($responsible, $room);
        $payload['guests'][] = [
            'key' => 'companion',
            'identification_type_id' => $identificationType->id,
            'first_name' => 'Luisa',
            'last_name' => 'Díaz',
            'identification_number' => '900002',
        ];
        $payload['room_occupancies'][0]['guest_keys'][] = 'companion';

        $this->post(route('hotels.stays.store', $hotel), $payload)
            ->assertSessionHasErrors('room_occupancies.0.guest_keys');
    }

    #[Test]
    public function it_rejects_a_guest_or_room_from_another_hotel(): void
    {
        $hotel = Hotel::factory()->create();
        $otherHotel = Hotel::factory()->create();
        $guest = Guest::factory()->for($otherHotel)->create();
        [$room] = $this->rooms($hotel);

        $this->post(route('hotels.stays.store', $hotel), $this->payload($guest, $room))
            ->assertSessionHasErrors('guests.0.guest_id');

        $localGuest = Guest::factory()->for($hotel)->create();
        [$foreignRoom] = $this->rooms($otherHotel);

        $this->post(route('hotels.stays.store', $hotel), $this->payload($localGuest, $foreignRoom))
            ->assertSessionHasErrors('room_occupancies.0.room_id');
    }

    #[Test]
    public function it_rejects_an_inactive_or_occupied_room(): void
    {
        $hotel = Hotel::factory()->create();
        $guest = Guest::factory()->for($hotel)->create();
        [$room] = $this->rooms($hotel);
        $room->update(['is_active' => false]);

        $this->post(route('hotels.stays.store', $hotel), $this->payload($guest, $room))
            ->assertSessionHasErrors('room_occupancies.0.room_id');

        $room->update(['is_active' => true]);
        $occupyingStay = Stay::factory()->for($hotel)->create(['responsible_guest_id' => $guest->id]);
        RoomOccupancy::factory()->for($occupyingStay)->for($room)->create();

        $this->post(route('hotels.stays.store', $hotel), $this->payload($guest, $room))
            ->assertSessionHasErrors('room_occupancies.0.room_id');
    }

    #[Test]
    public function it_rejects_a_duplicate_new_guest_and_an_invalid_check_out_date(): void
    {
        $hotel = Hotel::factory()->create();
        $identificationType = IdentificationType::factory()->create();
        $responsible = Guest::factory()->for($hotel)->create(['identification_type_id' => $identificationType->id]);
        Guest::factory()->for($hotel)->create([
            'identification_type_id' => $identificationType->id,
            'identification_number' => 'already-registered',
        ]);
        [$room] = $this->rooms($hotel);
        $payload = $this->payload($responsible, $room);
        $payload['guests'][] = [
            'key' => 'duplicate',
            'identification_type_id' => $identificationType->id,
            'first_name' => 'Duplicate',
            'last_name' => 'Guest',
            'identification_number' => 'already-registered',
        ];
        $payload['room_occupancies'][0]['guest_keys'][] = 'duplicate';

        $this->post(route('hotels.stays.store', $hotel), $payload)->assertSessionHasErrors('guests');

        $payload = $this->payload($responsible, $room);
        $payload['expected_check_out_on'] = now()->toDateString();

        $this->post(route('hotels.stays.store', $hotel), $payload)
            ->assertSessionHasErrors('expected_check_out_on');
    }

    /** @return array<int, Room> */
    private function rooms(Hotel $hotel): array
    {
        $roomType = RoomType::factory()->for($hotel)->create(['capacity' => 2]);

        return [
            Room::factory()->for($hotel)->for($roomType)->create(['number' => '101']),
            Room::factory()->for($hotel)->for($roomType)->create(['number' => '102']),
        ];
    }

    /** @return array<string, mixed> */
    private function payload(Guest $guest, Room $room): array
    {
        return [
            'expected_check_out_on' => now()->addDay()->toDateString(),
            'responsible_guest_key' => 'responsible',
            'guests' => [['key' => 'responsible', 'guest_id' => $guest->id]],
            'room_occupancies' => [[
                'room_id' => $room->id,
                'nightly_rate' => '150000.00',
                'guest_keys' => ['responsible'],
            ]],
        ];
    }
}
