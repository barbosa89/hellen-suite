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
            'confirmed' => true,
        ]);
    }
}
