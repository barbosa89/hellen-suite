<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use App\Http\Requests\{AddRooms, StoreGuest};
use App\Helpers\{Boolean, Fields, Id, Random};
use App\Welkome\{Guest, IdentificationType, Invoice, Room};

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
            ->get(Fields::get('invoices'));

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
        $invoice->status = true;
        $invoice->reservation = Boolean::get($request->get('reservation'));
        $invoice->for_company = Boolean::get($request->get('for_company'));
        $invoice->are_tourists = Boolean::get($request->get('are_tourists'));
        $invoice->user()->associate(auth()->user()->parent);

        if ($invoice->save()) {
            return redirect()->route('invoices.show', [
                'id' => Hashids::encode($invoice->id)
            ]);
        }

        flash(trans('common.error'))->error();

        return redirect()->route('invoices.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $invoice = Invoice::where('user_id', auth()->user()->parent)
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->with([
                'guest' => function ($query) {
                    $query->select(Fields::get('guests'));
                },
                'company' => function ($query) {
                    $query->select(['id', 'name', 'tin']);
                },
                'rooms' => function ($query) {
                    $query->select(Fields::parsed('rooms'));
                }
            ])->first(Fields::parsed('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        $rooms = Room::where('user_id', auth()->user()->parent)
            ->where('status', '1')
            ->get(Fields::get('rooms'));

        return view('app.invoices.show', compact('invoice', 'rooms'));
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
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->first(Fields::get('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        $rooms = Room::where('user_id', auth()->user()->parent)
            ->where('status', '1')
            ->get(Fields::get('rooms'));

        return view('app.invoices.add-rooms', compact('invoice', 'rooms'));
    }

    /**
     * Attach the selected rooms to invoice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function storeRooms(AddRooms $request, $id)
    {
        $status = false;

        \DB::transaction(function () use (&$status, $request, $id) {
            try {
                $invoice = Invoice::where('user_id', auth()->user()->parent)
                    ->where('id', Id::get($id))
                    ->where('open', true)
                    ->where('status', true)
                    ->first(Fields::parsed('invoices'));
    
                $room = Room::where('user_id', auth()->user()->parent)
                    ->where('id', Id::get($request->room))
                    ->where('status', '1')
                    ->first(Fields::get('rooms'));
    
                $start = Carbon::createFromFormat('Y-m-d', $request->start);
                $end = Carbon::createFromFormat('Y-m-d', $request->end);
                $quantity = $start->diffInDays($end);
                $value = $quantity * $room->value;
    
                $invoice->rooms()->attach(
                    $room->id,
                    [
                        'quantity' => $quantity,
                        'value' => $value,
                        'start' => $request->start,
                        'end' => $request->get('end', null)
                    ]
                );

                $invoice->subvalue += $value;
                $invoice->value += $value;
                $invoice->update();

                $room->status = '0';
                $room->update();
                $status = true;
            } catch (\Throwable $e) {
                // ..
            }
        });

        if ($status) {
            flash(trans('common.successful'))->success();
        } else {
            flash(trans('common.error'))->error();
        }

        return back();
    }

    /**
     * Display a listing of searched records.
     *
     * @param  \App\Welkome\Guest  $guest
     * @return \Illuminate\Http\Response
     */
    public function searchGuests($id)
    {   
        $invoice = Invoice::where('user_id', auth()->user()->parent)
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->with([
                'rooms' => function ($query) {
                    $query->select('id');
                },
                'rooms.guests' => function ($query) {
                    $query->select('id', 'name', 'last_name');
                }
            ])->first(Fields::parsed('invoices'));

        if (empty($invoice)) {
            abort(404);
        }
        
        return view('app.invoices.search-guests', compact('invoice'));
    }

    /**
     * Show the form for creating a new invoice guest.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function createGuests($id)
    {   
        $invoice = Invoice::where('user_id', auth()->user()->parent)
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->with([
                'rooms' => function ($query) {
                    $query->select('id', 'number');
                }
            ])->first(['id']);

        if (empty($invoice)) {
            abort(404);
        }
        
        $types = IdentificationType::all(['id', 'type']);
        
        return view('app.invoices.guests.create', compact('invoice', 'types'));
    }

    /**
     * Store a newly created guest in storage and attaching to invoice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function storeGuests(storeGuest $request, $id)
    {
        $invoice = Invoice::where('user_id', auth()->user()->parent)
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->with([
                'guest' => function ($query) {
                    $query->select('id');
                },
            ])->first(['id', 'guest_id']);

        if (empty($invoice)) {
            abort(404);
        }

        $guest = new Guest();
        $guest->name = $request->name;
        $guest->last_name = $request->last_name;
        $guest->dni = $request->dni;
        $guest->email = $request->get('email', null);
        $guest->gender = $request->get('gender', null);
        $guest->birthdate = $request->get('birthdate', null);
        $guest->name = $request->get('name', null);
        $guest->identificationType()->associate(Id::get($request->type));
        $guest->user()->associate(auth()->user()->parent);

        if ($guest->save()) {
            $invoice->guest()->associate($guest->id);
            $invoice->update();

            $guest->rooms()->attach(Id::get($request->room), [
                'invoice_id' => $invoice->id
            ]);

            flash(trans('common.successful'))->success();

            return back();
        }

        flash(trans('common.error'))->error();

        return back();
    }
}
