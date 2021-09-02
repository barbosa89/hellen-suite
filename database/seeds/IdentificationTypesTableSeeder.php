<?php

use App\Constants\IdentificationTypes;
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
        foreach (IdentificationTypes::toArray() as $type) {
            IdentificationType::create(['type' => $type]);
        }
    }
}
