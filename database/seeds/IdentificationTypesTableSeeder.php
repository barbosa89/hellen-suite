<?php

use Illuminate\Database\Seeder;
use App\Welkome\IdentificationType;

class IdentificationTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        IdentificationType::create(['type' => 'cc']);
        IdentificationType::create(['type' => 'ci']);
        IdentificationType::create(['type' => 'dni']);
        IdentificationType::create(['type' => 'dui']);
        IdentificationType::create(['type' => 'ec']);
        IdentificationType::create(['type' => 'it']);
        IdentificationType::create(['type' => 'rc']);
        IdentificationType::create(['type' => 'tp']);
    }
}
