<?php

namespace App\Events;

use App\Models\Guest;
use App\Models\Room;
use App\Models\Voucher;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CheckOut
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Voucher $voucher;

    public Guest $guest;

    public Room $room;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Voucher $voucher, Guest $guest, Room $room)
    {
        $this->guest = $guest;
        $this->voucher = $voucher;
        $this->room = $room;
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
