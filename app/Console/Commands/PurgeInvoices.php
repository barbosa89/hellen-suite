<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\User;
use Illuminate\Console\Command;

class PurgeInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:purge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Destroy all old pending invoices';

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
     * @return int
     */
    public function handle()
    {
        $rows = Invoice::whereIn('status', [Invoice::CANCELED, Invoice::PENDING])
            ->whereDate('created_at', '<=', now()->subDay())
            ->delete();

        $this->info("Affected records: {$rows}");

        return 0;
    }
}
