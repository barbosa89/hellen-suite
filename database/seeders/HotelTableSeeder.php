<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tag;
use App\Models\Note;
use App\Models\Room;
use App\Models\Hotel;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Database\Seeder;

class HotelTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('email', 'manager@dev.com')->first(['id']);

        factory(Hotel::class, 2)->create([
            'user_id' => $user->id
        ])->each(function ($hotel) use ($user) {
            $hotel->rooms()->saveMany(factory(Room::class, 10)->make([
                'user_id' => $user->id
            ]));

            $hotel->products()->saveMany(factory(Product::class, 10)->make([
                'user_id' => $user->id
            ]));

            $hotel->services()->saveMany(factory(Service::class, 30)->make([
                'user_id' => $user->id
            ]));

            $hotel->notes()->saveMany(factory(Note::class, 30)->make([
                'user_id' => $user->id
            ]));

            $hotel->notes->each(function ($note) use ($user)
            {
                $note->tags()->saveMany(factory(Tag::class, 3)->make([
                    'user_id' => $user->id
                ]));
            });
        });
    }
}
