<?php

declare(strict_types=1);

use Database\Seeders\ReferenceDataSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

return new class() extends Migration
{
    public function up(): void
    {
        $exitCode = Artisan::call('db:seed', [
            '--class' => ReferenceDataSeeder::class,
            '--force' => true,
        ]);

        if ($exitCode !== 0) {
            throw new RuntimeException('Unable to seed the reference data.');
        }
    }
};
