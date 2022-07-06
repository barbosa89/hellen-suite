<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;
use Spatie\Permission\Models\Permission;

class SyncUserPermissions extends Command
{
    /**
     * @var string
     */
    protected $signature = 'permissions:sync';

    /**
     * @var string
     */
    protected $description = 'Sync manager user permissions';

    public function handle(): int
    {
        DB::transaction(function () {
            $permissions = Permission::all(['id', 'name', 'guard_name']);

            LazyCollection::make(function () {
                return User::owner()->get(['id']);
            })->each(function (User $user) use ($permissions) {
                $user->syncPermissions($permissions);
            });
        });

        return self::SUCCESS;
    }
}
