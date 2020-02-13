<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SaveTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:seeder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save in seeders the test data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $tables = [
            'companies',
            'guests',
            'guest_voucher',
            'guest_room',
            'hotels',
            'hotel_user',
            'vouchers',
            'voucher_room',
            'model_has_permissions',
            'model_has_roles',
            'role_has_permissions',
            'rooms',
            'users'
        ];

        Artisan::call('iseed ' . implode(',', $tables) .  ' --force');
    }
}
