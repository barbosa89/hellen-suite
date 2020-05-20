<?php

namespace App\Observers;

use App\Helpers\Fields;
use App\Welkome\Voucher;
use Illuminate\Support\Facades\DB;

class VoucherObserver
{
    /**
     * Handle to the voucher "created" event.
     *
     * @param  \App\Welkome\Voucher  $voucher
     * @return void
     */
    public function created(Voucher $voucher)
    {
        //
    }

    /**
     * Handle the voucher "updated" event.
     *
     * @param  \App\Welkome\Voucher  $voucher
     * @return void
     */
    public function updated(Voucher $voucher)
    {
        //
    }

    /**
     * Handle the voucher "deleting" event.
     *
     * @param  \App\Welkome\Voucher  $voucher
     * @return void
     */
    public function deleting(Voucher $voucher)
    {
        $voucher->load([
            'guests' => function ($query) {
                $query->select(fields_get('guests'));
            },
            'rooms' => function ($query) {
                $query->select(fields_dotted('rooms'));
            },
        ]);

        $voucher->rooms->each(function ($room, $index) use ($voucher) {
            $room->number = $room->number;
            $room->description = $room->description;
            $room->status = '1';
            $room->save();
        });

        $voucher->guests->each(function ($guest, $index) {
            $guest->dni = $guest->dni;
            $guest->name = $guest->name;
            $guest->last_name = $guest->last_name;
            $guest->email = $guest->email;
            $guest->status = false;
            $guest->save();
        });

        DB::table('guest_room')
            ->where('voucher_id', $voucher->id)
            ->delete();
    }

    /**
     * Handle the voucher "deleted" event.
     *
     * @param  \App\Welkome\Voucher  $voucher
     * @return void
     */
    public function deleted(Voucher $voucher)
    {
        //
    }
}
