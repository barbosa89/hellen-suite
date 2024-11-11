<?php

namespace App\Listeners;

use App\Events\CheckIn;
use App\Models\Check;

class RegisterCheckIn
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
     * @return void
     */
    public function handle(CheckIn $event)
    {
        $checkIn = new Check;
        $checkIn->in_at = now();
        $checkIn->guest()->associate($event->guest);
        $checkIn->voucher()->associate($event->voucher);
        $checkIn->save();

        notary($event->voucher->hotel)->checkinGuest($event->voucher, $event->guest, $event->room);
    }
}
