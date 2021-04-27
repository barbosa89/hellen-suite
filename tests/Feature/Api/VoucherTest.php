<?php

namespace Tests\Feature\Api;

use App\User;
use Tests\TestCase;
use App\Models\Room;
use App\Models\Check;
use App\Models\Guest;
use App\Models\Hotel;
use RolesTableSeeder;
use App\Models\Voucher;
use Carbon\CarbonPeriod;
use CountriesTableSeeder;
use PermissionsTableSeeder;
use Illuminate\Support\Carbon;
use IdentificationTypesTableSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;

class VoucherTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesTableSeeder::class);
        $this->seed(PermissionsTableSeeder::class);
        $this->seed(IdentificationTypesTableSeeder::class);
        $this->seed(CountriesTableSeeder::class);
    }

    public function test_access_is_denied_if_user_dont_have_vouchers_index_permissions()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        /** @var Hotel $hotel */
        $hotel = factory(Hotel::class)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->get("/api/v1/web/hotels/{$hotel->hash}/vouchers");

        $response->assertForbidden();
    }

    public function test_user_can_list_vouchers()
    {
        /** @var User $manager */
        $manager = factory(User::class)->create();
        $manager->givePermissionTo('vouchers.index');

        /** @var Hotel $hotel */
        $hotel = factory(Hotel::class)->create([
            'user_id' => $manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create([
            'hotel_id' => $hotel->id,
            'user_id' => $manager->id,
        ]);

        $response = $this->actingAs($manager)
            ->get("/api/v1/web/hotels/{$hotel->hash}/vouchers");

        $response->assertOk()
            ->assertJsonFragment([
                'hash' => id_encode($voucher->id),
                'number' => (string) $voucher->number,
            ]);
    }

    public function test_user_can_filter_new_vouchers_by_date()
    {
        /** @var User $manager */
        $manager = factory(User::class)->create();
        $manager->givePermissionTo('vouchers.index');

        /** @var Hotel $hotel */
        $hotel = factory(Hotel::class)->create([
            'user_id' => $manager->id,
        ]);

        /** @var Voucher $oldVoucher */
        $oldVoucher = factory(Voucher::class)->create([
            'hotel_id' => $hotel->id,
            'user_id' => $manager->id,
            'created_at' => now()->subDays(8),
        ]);

        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create([
            'hotel_id' => $hotel->id,
            'user_id' => $manager->id,
        ]);

        $response = $this->actingAs($manager)
            ->call(
                'GET',
                "/api/v1/web/hotels/{$hotel->hash}/vouchers",
                [
                    'from_date' => now()->subDays(7)->format('Y-m-d'),
                ]
            );

        $response->assertOk()
            ->assertJsonFragment([
                'number' => (string) $voucher->number,
            ])
            ->assertJsonMissing([
                'number' => (string) $oldVoucher->number,
            ]);
    }

    public function test_the_date_cannot_be_older_than_one_year()
    {
        /** @var User $manager */
        $manager = factory(User::class)->create();
        $manager->givePermissionTo('vouchers.index');

        /** @var Hotel $hotel */
        $hotel = factory(Hotel::class)->create([
            'user_id' => $manager->id,
        ]);

        $response = $this->actingAs($manager)
            ->call(
                'GET',
                "/api/v1/web/hotels/{$hotel->hash}/vouchers",
                [
                    'from_date' => now()->subYears(2),
                ]
            );

        $response->assertRedirect()
            ->assertSessionHasErrors('from_date');
    }

    public function test_user_can_get_guest_datasets_from_vouchers_history()
    {
        /** @var User $manager */
        $manager = factory(User::class)->create();
        $manager->givePermissionTo('vouchers.index');

        /** @var Hotel $hotel */
        $hotel = factory(Hotel::class)->create([
            'user_id' => $manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create([
            'open' => true,
            'status' => true,
            'user_id' => $manager->id,
            'hotel_id' => $hotel->id,
        ]);

        /** @var Room $room */
        $room = factory(Room::class)->create([
            'hotel_id' => $hotel->id,
            'user_id' => $manager->id,
        ]);

        $voucher->rooms()->attach(
            $room->id,
            [
                'price' => $room->price,
                'quantity' => 1,
                'discount' => 0,
                'subvalue' => $room->price,
                'taxes' => 0,
                'value' => $room->price,
                'start' => now(),
                'end' => now(),
                'enabled' => true
            ]
        );

        /** @var Guest $guest */
        $guest = factory(Guest::class)->create([
            'user_id' => $manager->id,
            'status' => false,
        ]);

        $voucher->guests()->attach($guest->id, [
            'main' => true,
            'active' => true,
        ]);

        $guest->rooms()->attach($room, [
            'voucher_id' => $voucher->id
        ]);

        factory(Check::class)->create([
            'in_at' => now(),
            'out_at' => null,
            'guest_id' => $guest->id,
            'voucher_id' => $voucher->id,
        ]);

        $date = now();

        $response = $this->actingAs($manager)
            ->get("/api/v1/web/hotels/{$hotel->hash}/vouchers/datasets/guests/{$date->format('Y-m-d')}");

        $labels = $this->generateLabels($date);
        $colors = $this->generateColors($labels);

        $response->assertOk()
            ->assertJson([
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => trans('guests.title'),
                        'data' => $this->generateData($labels, $date),
                        'backgroundColor' => $colors['backgroundColor'],
                        'borderColor' => $colors['borderColor'],
                    ]
                ]
            ]);
    }

    /**
     * @param string $column
     * @param bool $status
     * @param string $filterValue
     * @dataProvider statusProvider
     */
    public function test_user_can_filter_vouchers_by_status(string $column, bool $status, string $filterValue)
    {
        /** @var User $manager */
        $manager = factory(User::class)->create();
        $manager->givePermissionTo('vouchers.index');

        /** @var Hotel $hotel */
        $hotel = factory(Hotel::class)->create([
            'user_id' => $manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create([
            'hotel_id' => $hotel->id,
            'user_id' => $manager->id,
            $column => !$status,
        ]);

        /** @var Voucher $filterableVoucher */
        $filterableVoucher = factory(Voucher::class)->create([
            'hotel_id' => $hotel->id,
            'user_id' => $manager->id,
            $column => $status,
        ]);

        $response = $this->actingAs($manager)
            ->call(
                'GET',
                "/api/v1/web/hotels/{$hotel->hash}/vouchers",
                [
                    'status' => [$filterValue],
                ]
            );

        $response->assertOk()
            ->assertJsonFragment([
                'number' => (string) $filterableVoucher->number,
            ])
            ->assertJsonMissing([
                'number' => (string) $voucher->number,
            ]);
    }

    /**
     * @param string $type
     * @dataProvider typeProvider
     */
    public function test_user_can_filter_vouchers_by_type(string $type)
    {
        /** @var User $manager */
        $manager = factory(User::class)->create();
        $manager->givePermissionTo('vouchers.index');

        /** @var Hotel $hotel */
        $hotel = factory(Hotel::class)->create([
            'user_id' => $manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create([
            'hotel_id' => $hotel->id,
            'user_id' => $manager->id,
            'type' => Arr::first(Voucher::TYPES, fn($t) => $t !== $type),
        ]);

        /** @var Voucher $filterableVoucher */
        $filterableVoucher = factory(Voucher::class)->create([
            'hotel_id' => $hotel->id,
            'user_id' => $manager->id,
            'type' => $type,
        ]);

        $response = $this->actingAs($manager)
            ->call(
                'GET',
                "/api/v1/web/hotels/{$hotel->hash}/vouchers",
                [
                    'type' => [$type],
                ]
            );

        $response->assertOk()
            ->assertJsonFragment([
                'number' => (string) $filterableVoucher->number,
            ])
            ->assertJsonMissing([
                'number' => (string) $voucher->number,
            ]);
    }


    public function test_user_can_search_vouchers()
    {
        /** @var User $manager */
        $manager = factory(User::class)->create();
        $manager->givePermissionTo('vouchers.index');

        /** @var Hotel $hotel */
        $hotel = factory(Hotel::class)->create([
            'user_id' => $manager->id,
        ]);

        /** @var Voucher $voucher */
        $voucher = factory(Voucher::class)->create([
            'hotel_id' => $hotel->id,
            'user_id' => $manager->id,
        ]);

        /** @var Voucher $searchableVoucher */
        $searchableVoucher = factory(Voucher::class)->create([
            'hotel_id' => $hotel->id,
            'user_id' => $manager->id,
        ]);

        $response = $this->actingAs($manager)
            ->call(
                'GET',
                "/api/v1/web/hotels/{$hotel->hash}/vouchers",
                [
                    'search' => $searchableVoucher->number,
                ]
            );

        $response->assertOk()
            ->assertJsonFragment([
                'number' => (string) $searchableVoucher->number,
            ])
            ->assertJsonMissing([
                'number' => (string) $voucher->number,
            ]);
    }

    private function generateLabels(Carbon $date): array
    {
        $start = $date->copy()->startOfMonth();
        $end = $date->copy()->endOfMonth();

        $period = CarbonPeriod::create($start, $end);
        $labels = [];

        foreach ($period as $day) {
            $labels[] = $day->format('Y-m-d');
        }

        return $labels;
    }

    private function generateData(array $labels, Carbon $date): array
    {
        $data = [];

        foreach ($labels as $label) {
            if ($label == $date->format('Y-m-d')) {
                $data[] = 1;
            } else {
                $data[] = 0;
            }
        }

        return $data;
    }

    private function generateColors(array $labels): array
    {
        $colors = get_colors();
        $index = 0;
        $chartColors = [];

        foreach ($labels as $label) {
            $chartColors['backgroundColor'][] = $colors[$index]['bar'];
            $chartColors['borderColor'][] = $colors[$index]['border'];

            $index++;

            if ($index == 6) {
                $index = 0;
            }
        }

        return $chartColors;
    }

    public function statusProvider(): array
    {
        return [
            'open vouchers' => [
                'open',
                true,
                Voucher::OPEN,
            ],
            'closed vouchers' => [
                'open',
                false,
                Voucher::CLOSED,
            ],
            'paid vouchers' => [
                'payment_status',
                true,
                Voucher::PAID,
            ],
            'vouchers pending payment' => [
                'payment_status',
                false,
                Voucher::PENDING,
            ],
            'reservation vouchers' => [
                'reservation',
                true,
                Voucher::RESERVATION,
            ],
        ];
    }

    public function typeProvider(): array
    {
        return [
            'vouchers of sale type' => [Voucher::SALE],
            'vouchers of entry type' => [Voucher::ENTRY],
            'vouchers of loss type' => [Voucher::LOSS],
            'vouchers of discard type' => [Voucher::DISCARD],
            'vouchers of sale lodging' => [Voucher::LODGING],
            'vouchers of sale dining' => [Voucher::DINING],
        ];
    }
}
