<?php

namespace App\Listeners;

use App\Services\PermissionCache;

class StorePermissionsInCache
{
    public function handle()
    {
        PermissionCache::get();
    }
}
