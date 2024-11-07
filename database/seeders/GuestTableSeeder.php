<?php

namespace Database\Seeders;

use App\Models\Guest;
use App\Models\User;
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
        $user = User::where('email', 'manager@app.com')->first(['id']);

        Guest::factory(20)->create([
            'user_id' => $user->id,
        ]);
    }
}
