<?php

namespace App\Observers;

use App\Helpers\Fields;
use App\Welkome\Invoice;

class InvoiceObserver
{
    /**
     * Handle to the invoice "created" event.
     *
     * @param  \App\Welkome\Invoice  $invoice
     * @return void
     */
    public function created(Invoice $invoice)
    {
        //
    }

    /**
     * Handle the invoice "updated" event.
     *
     * @param  \App\Welkome\Invoice  $invoice
     * @return void
     */
    public function updated(Invoice $invoice)
    {
        //
    }

    /**
     * Handle the invoice "deleting" event.
     *
     * @param  \App\Welkome\Invoice  $invoice
     * @return void
     */
    public function deleting(Invoice $invoice)
    {
        $invoice->load([
            'guests' => function ($query) {
                $query->select(Fields::get('guests'));
            },
            'rooms' => function ($query) {
                $query->select(Fields::parsed('rooms'));
            },
        ]);

        $invoice->rooms->each(function ($room, $index) use ($invoice) {
            $room->number = $room->number;
            $room->description = $room->description;
            $room->status = '1';
            $room->save(); 
        });

        $invoice->guests->each(function ($guest, $index) {
            $guest->dni = $guest->dni;
            $guest->name = $guest->name;
            $guest->last_name = $guest->last_name;
            $guest->email = $guest->email;
            $guest->status = false;
            $guest->save();
        });

        \DB::table('guest_room')
            ->where('invoice_id', $invoice->id)
            ->delete();
    }

    /**
     * Handle the invoice "deleted" event.
     *
     * @param  \App\Welkome\Invoice  $invoice
     * @return void
     */
    public function deleted(Invoice $invoice)
    {
        //
    }
}
