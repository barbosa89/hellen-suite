<?php

declare(strict_types=1);

namespace Tests\Feature\Stays;

use App\Constants\StayGuestRole;
use App\Constants\StayStatus;
use App\Models\Guest;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomOccupancy;
use App\Models\RoomType;
use App\Models\Stay;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StayGuestStoreTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_adds_an_existing_guest_to_an_active_stay(): void
    {
        [$hotel, $stay, $occupancy] = $this->activeStay();
        $guest = Guest::factory()->for($hotel)->create();

        $this->post(route('hotels.stays.guests.store', [$hotel, $stay]), [
            'room_occupancy_id' => $occupancy->id,
            'guest_id' => $guest->id,
        ])->assertSessionHasNoErrors();

        $stayGuest = $stay->stayGuests()->where('guest_id', $guest->id)->sole();

        $this->assertSame(StayGuestRole::Companion, $stayGuest->role);
        $this->assertTrue($occupancy->guests()->whereKey($guest->id)->exists());
    }

    #[Test]
    public function it_creates_and_adds_a_new_guest_to_an_active_stay(): void
    {
        [$hotel, $stay, $occupancy, $responsibleGuest] = $this->activeStay();

        $this->post(route('hotels.stays.guests.store', [$hotel, $stay]), [
            'room_occupancy_id' => $occupancy->id,
            'identification_type_id' => $responsibleGuest->identification_type_id,
            'first_name' => 'Laura',
            'last_name' => 'Martínez',
            'identification_number' => '1030000001',
            'mobile' => '3001234567',
            'email' => 'laura@example.com',
        ])->assertSessionHasNoErrors();

        $guest = $hotel->guests()
            ->where('identification_number', '1030000001')
            ->sole();

        $this->assertSame('Laura', $guest->first_name);
        $this->assertSame(StayGuestRole::Companion, $stay->stayGuests()->where('guest_id', $guest->id)->sole()->role);
        $this->assertTrue($occupancy->guests()->whereKey($guest->id)->exists());
    }

    #[Test]
    public function it_rejects_a_guest_when_the_stay_is_closed(): void
    {
        [$hotel, $stay, $occupancy] = $this->activeStay();
        $guest = Guest::factory()->for($hotel)->create();
        $stay->update([
            'status' => StayStatus::CheckedOut,
            'checked_out_at' => now(),
        ]);

        $this->post(route('hotels.stays.guests.store', [$hotel, $stay]), [
            'room_occupancy_id' => $occupancy->id,
            'guest_id' => $guest->id,
        ])->assertSessionHasErrors('room_occupancy_id');

        $this->assertSame(1, $stay->stayGuests()->count());
    }

    #[Test]
    public function it_rejects_a_closed_or_unrelated_occupancy(): void
    {
        [$hotel, $stay, $occupancy] = $this->activeStay();
        $guest = Guest::factory()->for($hotel)->create();
        $occupancy->update(['checked_out_at' => now()]);

        $this->post(route('hotels.stays.guests.store', [$hotel, $stay]), [
            'room_occupancy_id' => $occupancy->id,
            'guest_id' => $guest->id,
        ])->assertSessionHasErrors('room_occupancy_id');

        $otherStay = Stay::factory()->for($hotel)->create();
        $otherOccupancy = RoomOccupancy::factory()
            ->for($otherStay)
            ->for(Room::factory()->for($hotel))
            ->create();

        $this->post(route('hotels.stays.guests.store', [$hotel, $stay]), [
            'room_occupancy_id' => $otherOccupancy->id,
            'guest_id' => $guest->id,
        ])->assertSessionHasErrors('room_occupancy_id');

        $this->assertSame(1, $stay->stayGuests()->count());
    }

    #[Test]
    public function it_rejects_a_guest_when_the_selected_room_is_full_without_persisting_a_profile(): void
    {
        [$hotel, $stay, $occupancy, $responsibleGuest] = $this->activeStay(1);

        $this->post(route('hotels.stays.guests.store', [$hotel, $stay]), [
            'room_occupancy_id' => $occupancy->id,
            'identification_type_id' => $responsibleGuest->identification_type_id,
            'first_name' => 'Laura',
            'last_name' => 'Martínez',
            'identification_number' => '1030000001',
        ])->assertSessionHasErrors('room_occupancy_id');

        $this->assertSame(1, $hotel->guests()->count());
        $this->assertSame(1, $stay->stayGuests()->count());
        $this->assertSame(1, $occupancy->guests()->count());
    }

    #[Test]
    public function it_rejects_a_guest_already_registered_in_the_stay(): void
    {
        [$hotel, $stay, $occupancy, $responsibleGuest] = $this->activeStay();

        $this->post(route('hotels.stays.guests.store', [$hotel, $stay]), [
            'room_occupancy_id' => $occupancy->id,
            'guest_id' => $responsibleGuest->id,
        ])->assertSessionHasErrors('guest_id');

        $this->assertSame(1, $stay->stayGuests()->count());
    }

    #[Test]
    public function it_rejects_a_guest_from_another_hotel(): void
    {
        [$hotel, $stay, $occupancy] = $this->activeStay();
        $guest = Guest::factory()->create();

        $this->post(route('hotels.stays.guests.store', [$hotel, $stay]), [
            'room_occupancy_id' => $occupancy->id,
            'guest_id' => $guest->id,
        ])->assertSessionHasErrors('guest_id');

        $this->assertSame(1, $stay->stayGuests()->count());
    }

    #[Test]
    public function it_rejects_a_new_guest_with_an_existing_document(): void
    {
        [$hotel, $stay, $occupancy, $responsibleGuest] = $this->activeStay();

        $this->post(route('hotels.stays.guests.store', [$hotel, $stay]), [
            'room_occupancy_id' => $occupancy->id,
            'identification_type_id' => $responsibleGuest->identification_type_id,
            'first_name' => 'Laura',
            'last_name' => 'Martínez',
            'identification_number' => $responsibleGuest->identification_number,
        ])->assertSessionHasErrors('identification_number');

        $this->assertSame(1, $hotel->guests()->count());
        $this->assertSame(1, $stay->stayGuests()->count());
    }

    /**
     * @return array{Hotel, Stay, RoomOccupancy, Guest}
     */
    private function activeStay(int $capacity = 2): array
    {
        $hotel = Hotel::factory()->create();
        $roomType = RoomType::factory()->for($hotel)->create(['capacity' => $capacity]);
        $room = Room::factory()->for($hotel)->for($roomType)->create();
        $responsibleGuest = Guest::factory()->for($hotel)->create();
        $stay = Stay::factory()->for($hotel)->create(['responsible_guest_id' => $responsibleGuest->id]);
        $stay->stayGuests()->create([
            'guest_id' => $responsibleGuest->id,
            'role' => StayGuestRole::Responsible,
        ]);
        $occupancy = RoomOccupancy::factory()->for($stay)->for($room)->create();
        $occupancy->guests()->attach($responsibleGuest->id);

        return [$hotel, $stay, $occupancy, $responsibleGuest];
    }
}
