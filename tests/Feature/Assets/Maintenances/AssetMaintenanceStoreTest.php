<?php

namespace Tests\Feature\Assets\Maintenances;

use Tests\TestCase;
use App\Models\Room;
use App\Models\User;
use App\Models\Asset;
use App\Models\Hotel;
use Illuminate\Support\Carbon;
use Tests\Traits\HasPermissions;
use Illuminate\Http\UploadedFile;
use Tests\Traits\HasFlashMessages;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AssetMaintenanceStoreTest extends TestCase
{
    use WithFaker;
    use HasPermissions;
    use RefreshDatabase;
    use HasFlashMessages;

    private string $route;
    private User $user;
    private Hotel $hotel;
    private Room $room;
    private Asset $asset;

    public function setUp(): void
    {
        parent::setUp();

        $this->createPermission('assets.edit');

        $this->user = User::factory()->create();
        $this->user->givePermissionTo('assets.edit');

        $this->hotel = Hotel::factory()
            ->for($this->user, 'owner')
            ->create();

        $this->room = Room::factory()
            ->for($this->hotel)
            ->for($this->user)
            ->create();

        $this->asset = Asset::factory()
            ->for($this->room)
            ->for($this->hotel)
            ->for($this->user)
            ->create();

        $this->route = route('assets.maintenance', ['id' => $this->asset->hash]);
    }

    public function test_guest_user_cannot_store_an_asset_maintenance(): void
    {
        $response = $this->get($this->route);

        $response->assertRedirect(route('login'));
    }

    public function test_unauthorized_user_cannot_store_an_asset_maintenance(): void
    {
        /** @var User $unauthorized */
        $unauthorized = User::factory()->create();

        $response = $this->actingAs($unauthorized)
            ->get($this->route);

        $response->assertForbidden();
    }

    public function test_authorized_user_can_store_an_asset_maintenance(): void
    {
        Storage::fake();

        $file = UploadedFile::fake()->create('document.pdf', 50);

        $data = [
            'date' => now()->format('Y-m-d'),
            'commentary' => $this->faker->sentence(3),
            'value' => $this->faker->randomNumber(4),
            'invoice' => $file,
        ];

        $response = $this->actingAs($this->user)
            ->post($this->route, $data);

        $response->assertSessionDoesntHaveErrors()
            ->assertRedirect();

        $this->asssertFlashMessage(trans('common.createdSuccessfully'), 'success');

        $this->assertDatabaseCount('maintenances', 1);

        $this->assertDatabaseHas('maintenances', [
            'date' => $data['date'],
            'value' => $data['value'],
            'commentary' => $data['commentary'],
            'maintainable_id' => $this->asset->id,
            'maintainable_type' => Asset::class,
        ]);

        Storage::assertExists("public/{$file->hashName()}");
    }

    /**
     * @param string $field
     * @param array $data
     * @dataProvider errorProvider
     */
    public function test_it_checks_validation_errors(string $field, array $data): void
    {
        $data = array_merge([
            'date' => now()->format('Y-m-d'),
            'commentary' => $this->faker->sentence(3),
            'value' => $this->faker->randomNumber(4),
        ], $data);

        $response = $this->actingAs($this->user)
            ->post($this->route, $data);

        $response->assertSessionHasErrors($field);
    }

    public function errorProvider(): array
    {
        return [
            'empty date' => [
                'field' => 'date',
                'data' => [
                    'date' => null,
                ],
            ],
            'wrong date' => [
                'field' => 'date',
                'data' => [
                    'date' => 'date',
                ],
            ],
            'date after today' => [
                'field' => 'date',
                'data' => [
                    'date' => Carbon::now()->addDay()->format('Y-m-d'),
                ],
            ],
            'empty commentary' => [
                'field' => 'commentary',
                'data' => [
                    'commentary' => null,
                ],
            ],
            'long commentary' => [
                'field' => 'commentary',
                'data' => [
                    'commentary' => str_repeat('commentary', 26),
                ],
            ],
            'non numeric value' => [
                'field' => 'value',
                'data' => [
                    'value' => 'value',
                ],
            ],
            'value under allowed min' => [
                'field' => 'value',
                'data' => [
                    'value' => 0,
                ],
            ],
            'value above allowed max' => [
                'field' => 'value',
                'data' => [
                    'value' => 100_000_000,
                ],
            ],
            'non file invoice' => [
                'field' => 'invoice',
                'data' => [
                    'invoice' => 'invoice',
                ],
            ],
            'invoice file exceeds max weight' => [
                'field' => 'invoice',
                'data' => [
                    'invoice' => UploadedFile::fake()->create('document.pdf', 201),
                ],
            ],
            'invalid invoice file type' => [
                'field' => 'invoice',
                'data' => [
                    'invoice' => UploadedFile::fake()->create('document.docx', 100),
                ],
            ],
        ];
    }
}
