<?php

namespace App\Events;

use App\Models\Guest;
use App\Models\Voucher;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CheckOut
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Guest $guest;

    public Voucher $voucher;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Guest $guest, Voucher $voucher)
    {
        $this->guest = $guest;
        $this->voucher = $voucher;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
