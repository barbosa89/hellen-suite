<?php
use App\User;
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
            'name' => 'Welkome', 
            'email' => 'root@welkome.com', 
            'password' => bcrypt('root'), 
            'status' => true, 
            'verified' => true
        ]);

        User::create([
            'name' => 'Admin', 
            'email' => 'admin@welkome.com', 
            'password' => bcrypt('admin'), 
            'status' => true, 
            'verified' => true
        ]);
    }
}
