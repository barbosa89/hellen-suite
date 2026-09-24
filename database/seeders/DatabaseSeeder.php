<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\User;
use App\Settings\GeneralSettings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            IdentificationTypeSeeder::class,
        ]);

        if (app()->environment('local')) {
            $user = User::query()->updateOrCreate(
                ['email' => 'test@example.com'],
                [
                    'name' => 'Local Admin',
                    'password' => 'password',
                ],
            );

            $user->forceFill(['email_verified_at' => now()])->save();

            $hotel = Hotel::query()->updateOrCreate(
                ['tin' => '900123456-7'],
                [
                    'business_name' => 'Hotel Hellen',
                    'address' => '100 Main Street',
                    'phone' => '+1 555 0100',
                    'mobile' => '+1 555 0101',
                    'email' => 'hotel@hellen.test',
                    'image' => null,
                ],
            );

            $singleRoomType = $hotel->roomTypes()->updateOrCreate(
                ['name' => 'Single'],
                ['capacity' => 1],
            );

            $doubleRoomType = $hotel->roomTypes()->updateOrCreate(
                ['name' => 'Double'],
                ['capacity' => 2],
            );

            foreach ([
                ['number' => '101', 'room_type_id' => $singleRoomType->id, 'reference_price' => 79],
                ['number' => '102', 'room_type_id' => $doubleRoomType->id, 'reference_price' => 119],
                ['number' => '103', 'room_type_id' => $doubleRoomType->id, 'reference_price' => 119],
            ] as $room) {
                $hotel->rooms()->updateOrCreate(
                    ['number' => $room['number']],
                    [
                        'room_type_id' => $room['room_type_id'],
                        'floor' => '1',
                        'reference_price' => $room['reference_price'],
                    ],
                );
            }

            $settings = app(GeneralSettings::class);
            $settings->currency = 'USD';
            $settings->save();
        }
    }
}
