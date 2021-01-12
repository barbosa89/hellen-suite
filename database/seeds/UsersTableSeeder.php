<?php
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

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
            'email' => 'root@dev.com',
            'password' => bcrypt('root'),
            'email_verified_at' => now(),
        ]);

        $manager = User::create([
            'name' => 'Manager',
            'email' => 'manager@dev.com',
            'password' => bcrypt('manager'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@dev.com',
            'password' => bcrypt('admin'),
            'email_verified_at' => now(),
            'parent' => $manager->id,
        ]);

        User::create([
            'name' => 'Accountant',
            'email' => 'accountant@dev.com',
            'password' => bcrypt('accountant'),
            'email_verified_at' => now(),
            'parent' => $manager->id,
        ]);

        User::create([
            'name' => 'Receptionist',
            'email' => 'receptionist@dev.com',
            'password' => bcrypt('receptionist'),
            'email_verified_at' => now(),
            'parent' => $manager->id,
        ]);

        User::create([
            'name' => 'Cashier',
            'email' => 'cashier@dev.com',
            'password' => bcrypt('cashier'),
            'email_verified_at' => now(),
            'parent' => $manager->id,
        ]);
    }
}
