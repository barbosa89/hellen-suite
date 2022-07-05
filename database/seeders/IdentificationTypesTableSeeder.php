<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IdentificationType;

class IdentificationTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        IdentificationType::insert([
            ['type' => 'cc'],
            ['type' => 'ci'],
            ['type' => 'dni'],
            ['type' => 'dui'],
            ['type' => 'ec'],
            ['type' => 'it'],
            ['type' => 'rc'],
            ['type' => 'tp'],
        ]);
    }
}
