<?php

namespace App\Listeners;

use App\Services\PermissionCache;
use Illuminate\Auth\Events\Logout;

class ForgetPermissionsFromCache
{
    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Logout $event)
    {
        PermissionCache::forget($event->user);
    }
}
