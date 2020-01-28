<?php

namespace App\Listeners;

use App\User;
use App\Welkome\Shift;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Storage;

class ShiftStart
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ShiftWatcher  $event
     * @return void
     */
    public function handle(Login $event)
    {
        // Get receptionist user
        $user = User::where('email', $event->user->email)
            ->whereHas('roles', function ($query)
            {
                $query->where('name', 'receptionist');
            })->with([
                'headquarters' => function($query) {
                    $query->select(['id', 'business_name']);
                }
            ])->first(['id', 'email']);

        if (!empty($user)) {
            $shift = new Shift();
            $shift->user()->associate($user);
            $shift->hotel()->associate($user->headquarters->first());
            $shift->save();
        }
    }
}
