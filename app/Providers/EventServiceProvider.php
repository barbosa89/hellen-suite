<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \App\Events\CheckIn::class => [
            \App\Listeners\RegisterCheckIn::class
        ],
        \App\Events\CheckOut::class => [
            \App\Listeners\RegisterCheckOut::class
        ],
        \App\Events\RoomCheckOut::class => [
            \App\Listeners\RegisterRoomCheckOut::class
        ],
        \Illuminate\Auth\Events\Login::class => [
            \App\Listeners\StorePermissionsInCache::class,
        ],
        \Illuminate\Auth\Events\Logout::class => [
            \App\Listeners\ForgetPermissionsFromCache::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
