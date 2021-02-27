<?php

namespace App\Listeners;

use App\Models\Check;
use App\Events\RoomCheckOut;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegisterRoomCheckOut
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
     * @param  RoomCheckOut  $event
     * @return void
     */
    public function handle(RoomCheckOut $event)
    {
        Check::where('voucher_id', $event->voucher->id)
            ->whereIn('guest_id', $event->voucher->guests->pluck('id')->toArray())
            ->whereNotNull('in_at')
            ->whereNull('out_at')
            ->update(['out_at' => now()]);

        notary($event->voucher->hotel)->checkoutGuests($event->voucher);
    }
}
