<?php

namespace App\Listeners;

use App\Events\CheckOut;
use App\Models\Check;

class RegisterCheckOut
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
    public function handle(CheckOut $event)
    {
        Check::where('voucher_id', $event->voucher->id)
            ->where('guest_id', $event->guest->id)
            ->whereNotNull('in_at')
            ->whereNull('out_at')
            ->update(['out_at' => now()]);

        notary($event->voucher->hotel)
            ->checkoutGuest(
                $event->voucher,
                $event->guest,
                $event->room
            );
    }
}
