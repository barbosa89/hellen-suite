<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Root',
            'email' => 'root@app.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        $manager = User::create([
            'name' => 'Manager',
            'email' => 'manager@app.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@app.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'parent' => $manager->id,
        ]);

        User::create([
            'name' => 'Accountant',
            'email' => 'accountant@app.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'parent' => $manager->id,
        ]);

        User::create([
            'name' => 'Receptionist',
            'email' => 'receptionist@app.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'parent' => $manager->id,
        ]);

        User::create([
            'name' => 'Cashier',
            'email' => 'cashier@app.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'parent' => $manager->id,
        ]);
    }
}
