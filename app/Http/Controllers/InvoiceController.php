<?php

namespace App\Http\Controllers;

use App\Welkome\Room;
use App\Helpers\Random;
use App\Welkome\Invoice;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoices = Invoice::where('user_id', auth()->user()->parent)
            ->where('open', true)
            ->where('status', true)
            ->get(config('welkome.fields.invoices'));

        return view('app.invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $invoice = new Invoice();
        $invoice->number = Random::consecutive();
        $invoice->subvalue = 0.0;
        $invoice->taxes = 0.0;
        $invoice->discount = 0.0;
        $invoice->value = 0.0;
        $invoice->user()->associate(auth()->user()->parent);

        if ($invoice->save()) {
            return redirect()->route('invoices.add.rooms', [
                'id' => Hashids::encode($invoice->id)
            ]);
        }

        flash(trans('common.error'))->error();

        return redirect()->route('invoices.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Welkome\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Welkome\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Welkome\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Welkome\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        //
    }

    /**
     * Show the form for adding rooms to invoice.
     * 
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function addRooms($id = '')
    {
        $invoice = Invoice::where('user_id', auth()->user()->parent)
            ->where('open', true)
            ->where('status', true)
            ->first(config('welkome.fields.invoices'));

        $rooms = Room::where('user_id', auth()->user()->parent)
            ->where('status', '1')
            ->get(config('welkome.fields.rooms'));

        return view('app.invoices.add-rooms', compact('invoice', 'rooms'));
    }

    /**
     * Attach the selected rooms to invoice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function attachRooms(Request $request, $id)
    {
        # code...
    }
}
