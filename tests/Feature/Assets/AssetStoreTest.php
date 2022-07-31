<?php

namespace Tests\Feature\Assets;

use Tests\TestCase;
use App\Models\Room;
use App\Models\User;
use Hashids\Hashids;
use App\Models\Asset;
use App\Models\Hotel;
use Tests\Traits\HasPermissions;
use Tests\Traits\HasFlashMessages;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AssetStoreTest extends TestCase
{
    use HasPermissions;
    use RefreshDatabase;
    use HasFlashMessages;

    private string $route;

    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->createPermission('assets.create');

        $this->route = route('assets.store');

        $this->user = User::factory()->create();
        $this->user->givePermissionTo('assets.create');
    }

    public function test_guest_user_cannot_create_assets(): void
    {
        $response = $this->post($this->route);

        $response->assertRedirect(route('login'));
    }

    public function test_unauthorized_user_cannot_create_assets(): void
    {
        /** @var User $unauthorized */
        $unauthorized = User::factory()->create();

        $response = $this->actingAs($unauthorized)
            ->post($this->route);

        $response->assertForbidden();
    }

    public function test_authorized_user_can_create_assets(): void
    {
        $hotel = Hotel::factory()
            ->for($this->user, 'owner')
            ->create();

        $room = Room::factory()
            ->for($hotel)
            ->for($this->user)
            ->create();

        $asset = Asset::factory()
            ->for($hotel)
            ->for($room)
            ->for($this->user)
            ->make();

        $data = [
            'number' => $asset->number,
            'description' => $asset->description,
            'brand' => $asset->brand,
            'model' => $asset->model,
            'serial_number' => $asset->serial_number,
            'price' => $asset->price,
            'location' => $asset->location,
            'room' => $room->hash,
            'hotel' => $hotel->hash,
        ];

        $response = $this->actingAs($this->user)
            ->post($this->route, $data);

        $response->assertSessionDoesntHaveErrors()
            ->assertRedirect();

        $this->asssertFlashMessage(trans('common.createdSuccessfully'), 'success');

        $this->assertDatabaseCount('assets', 1);

        $this->assertDatabaseHas('assets', [
            'number' => $asset->number,
            'description' => $asset->description,
            'brand' => $asset->brand,
            'model' => $asset->model,
            'serial_number' => $asset->serial_number,
            'price' => $asset->price,
            'location' => $asset->location,
            'room_id' => $room->id,
            'hotel_id' => $hotel->id,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_authorized_user_can_create_assets_without_room(): void
    {
        $hotel = Hotel::factory()
            ->for($this->user, 'owner')
            ->create();

        $room = Room::factory()
            ->for($hotel)
            ->for($this->user)
            ->create();

        $asset = Asset::factory()
            ->for($hotel)
            ->for($this->user)
            ->make();

        $data = [
            'number' => $asset->number,
            'description' => $asset->description,
            'brand' => $asset->brand,
            'model' => $asset->model,
            'serial_number' => $asset->serial_number,
            'price' => $asset->price,
            'location' => $asset->location,
            'hotel' => $hotel->hash,
        ];

        $response = $this->actingAs($this->user)
            ->post($this->route, $data);

        $response->assertSessionDoesntHaveErrors()
            ->assertRedirect();

        $this->asssertFlashMessage(trans('common.createdSuccessfully'), 'success');

        $this->assertDatabaseCount('assets', 1);

        $this->assertDatabaseHas('assets', [
            'number' => $asset->number,
            'description' => $asset->description,
            'brand' => $asset->brand,
            'model' => $asset->model,
            'serial_number' => $asset->serial_number,
            'price' => $asset->price,
            'location' => $asset->location,
            'room_id' => null,
            'hotel_id' => $hotel->id,
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * @param string $field
     * @param array $data
     * @dataProvider errorProvider
     */
    public function test_it_checks_validation_errors(string $field, array $data): void
    {
        $hotel = Hotel::factory()
            ->for($this->user, 'owner')
            ->create();

        $room = Room::factory()
            ->for($hotel)
            ->for($this->user)
            ->create();

        $asset = Asset::factory()
            ->for($hotel)
            ->for($room)
            ->for($this->user)
            ->make();

        $data = array_merge([
            'number' => $asset->number,
            'description' => $asset->description,
            'brand' => $asset->brand,
            'model' => $asset->model,
            'serial_number' => $asset->serial_number,
            'price' => $asset->price,
            'location' => $asset->location,
            'room' => $room->hash,
            'hotel' => $hotel->hash,
        ], $data);

        $response = $this->actingAs($this->user)
            ->post($this->route, $data);

        $response->assertSessionHasErrors($field);
    }

    private function hash(int $number): string
    {
        $hashids = new Hashids();

        return $hashids->encode($number);
    }

    public function errorProvider(): array
    {
        return [
            'empty number' => [
                'field' => 'number',
                'data' => [
                    'number' => '',
                ],
            ],
            'no string number' => [
                'field' => 'number',
                'data' => [
                    'number' => 123,
                ],
            ],
            'long number' => [
                'field' => 'number',
                'data' => [
                    'number' => str_repeat('123', 7),
                ],
            ],
            'empty description' => [
                'field' => 'description',
                'data' => [
                    'description' => '',
                ],
            ],
            'long description' => [
                'field' => 'description',
                'data' => [
                    'description' => str_repeat('description', 25),
                ],
            ],
            'long brand' => [
                'field' => 'brand',
                'data' => [
                    'brand' => str_repeat('brand', 25),
                ],
            ],
            'long model' => [
                'field' => 'model',
                'data' => [
                    'model' => str_repeat('model', 25),
                ],
            ],
            'long serial number' => [
                'field' => 'serial_number',
                'data' => [
                    'serial_number' => str_repeat('serial_number', 25),
                ],
            ],
            'non numeric price' => [
                'field' => 'price',
                'data' => [
                    'price' => 'price',
                ],
            ],
            'negative price' => [
                'field' => 'price',
                'data' => [
                    'price' => -1,
                ],
            ],
            'price greather than max' => [
                'field' => 'price',
                'data' => [
                    'price' => 1_000_000_000,
                ],
            ],
            'long location' => [
                'field' => 'location',
                'data' => [
                    'location' => str_repeat('location', 25),
                ],
            ],
            'numeric room identifier' => [
                'field' => 'room',
                'data' => [
                    'room' => 1,
                ],
            ],
            'non existing room identifier' => [
                'field' => 'room',
                'data' => [
                    'room' => $this->hash(100),
                ],
            ],
            'empty hotel identifier' => [
                'field' => 'hotel',
                'data' => [
                    'hotel' => '',
                ],
            ],
            'numeric hotel identifier' => [
                'field' => 'hotel',
                'data' => [
                    'hotel' => 1,
                ],
            ],
            'non existing hotel identifier' => [
                'field' => 'hotel',
                'data' => [
                    'hotel' => $this->hash(100),
                ],
            ],
        ];
    }
}
