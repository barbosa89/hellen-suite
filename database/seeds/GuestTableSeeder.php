<?php

use App\User;
use App\Welkome\Guest;
use Illuminate\Database\Seeder;

class GuestTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('email', 'manager@welkome.com')->first(['id']);

        factory(Guest::class, 20)->create([
            'user_id' => $user->id
        ]);
    }
}
