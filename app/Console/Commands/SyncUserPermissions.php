<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\LazyCollection;
use Spatie\Permission\Models\Permission;

class SyncUserPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync manager user permissions';

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
        DB::transaction(function () {
            $permissions = Permission::all(['id', 'name', 'guard_name']);

            LazyCollection::make(function () {
                return User::owner()->get(['id']);
            })->each(function (User $user) use ($permissions) {
                $user->syncPermissions($permissions);
            });
        });

        return 0;
    }
}
