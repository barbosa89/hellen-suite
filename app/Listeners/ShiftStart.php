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
     * @param  Login  $event
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
            ])->first(['id', 'email', 'parent']);

        // Check if there is an user with receptionist role
        if (!empty($user)) {
            // Query an open shift of current user
            $shift = Shift::where('open', true)
                ->where('team_member', $user->id)
                ->whereHas('hotel', function ($query) use ($user)
                {
                    $query->where('id', $user->headquarters->first()->id);
                })->whereHas('user', function ($query) use ($user)
                {
                    $query->where('id', $user->parent);
                })->first(['id', 'open', 'hotel_id', 'user_id']);

            // If there is not a shift, then new shift is created
            // Else, the user continues with the current shift
            if (empty($shift)) {
                $shift = new Shift();
                $shift->team_member = $user->id;
                $shift->user()->associate($user->parent);
                $shift->hotel()->associate($user->headquarters->first());
                $shift->save();
            }
        }
    }
}
