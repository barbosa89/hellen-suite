<?php

namespace App\Listeners;

use App\Events\CheckIn;
use App\Models\Check;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
     * @param  CheckIn  $event
     * @return void
     */
    public function handle(CheckIn $event)
    {
        $checkIn = new Check();
        $checkIn->in_at = now();
        $checkIn->guest()->associate($event->guest);
        $checkIn->voucher()->associate($event->voucher);
        $checkIn->save();

        notary($event->voucher->hotel)->checkinGuest($event->voucher, $event->guest, $event->voucher->rooms->first());
    }
}
