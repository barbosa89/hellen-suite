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
            'name' => 'Welkome',
            'email' => 'root@welkome.com',
            'password' => bcrypt('root'),
            'email_verified_at' => Carbon::now()->toDateTimeString(),
        ]);

        User::create([
            'name' => 'Manager',
            'email' => 'manager@welkome.com',
            'password' => bcrypt('manager'),
            'email_verified_at' => Carbon::now()->toDateTimeString(),
        ]);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@welkome.com',
            'password' => bcrypt('admin'),
            'email_verified_at' => Carbon::now()->toDateTimeString(),
            'parent' => 2
        ]);

        User::create([
            'name' => 'Accountant',
            'email' => 'accountant@welkome.com',
            'password' => bcrypt('accountant'),
            'email_verified_at' => Carbon::now()->toDateTimeString(),
            'parent' => 2
        ]);

        User::create([
            'name' => 'Recep',
            'email' => 'recep@welkome.com',
            'password' => bcrypt('recep'),
            'email_verified_at' => Carbon::now()->toDateTimeString(),
            'parent' => 2
        ]);
    }
}
