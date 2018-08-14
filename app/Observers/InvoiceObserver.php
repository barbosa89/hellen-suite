<?php

namespace App\Observers;

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
     * Handle the invoice "deleted" event.
     *
     * @param  \App\Welkome\Invoice  $invoice
     * @return void
     */
    public function deleted(Invoice $invoice)
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
        //
    }
}
