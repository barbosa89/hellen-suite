<?php

namespace App\Listeners;

use App\Models\Check;
use App\Events\CheckOut;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

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
     * @param  CheckOut  $event
     * @return void
     */
    public function handle(CheckOut $event)
    {
        $checkOut = Check::where('voucher_id', $event->voucher->id)
            ->where('guest_id', $event->guest->id)
            ->where('in_at', '!=', null)
            ->where('out_at', null)
            ->first(['id', 'out_at']);

        $checkOut->out_at = now();
        $checkOut->save();

        notary($event->voucher->hotel)
            ->checkoutGuest(
                $event->voucher,
                $event->guest,
                $event->guest->rooms->first()
            );
    }
}
