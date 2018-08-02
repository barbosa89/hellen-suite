<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use App\Helpers\{Age, Fields, Id, Random};
use App\Http\Requests\{AddRooms, StoreInvoiceGuest};
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // TODO: Eliminar todos los registros vacios
        
        $invoice = new Invoice();
        $invoice->number = Random::consecutive();
        $invoice->subvalue = 0.0;
        $invoice->taxes = 0.0;
        $invoice->discount = 0.0;
        $invoice->value = 0.0;
        $invoice->status = true;
        $invoice->reservation = Input::bool($request->get('reservation'));
        $invoice->for_company = Input::bool($request->get('for_company'));
        $invoice->are_tourists = Input::bool($request->get('are_tourists'));
        $invoice->user()->associate(auth()->user()->parent);

        if ($invoice->save()) {
            return redirect()->route('invoices.rooms', [
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
        $id = Id::get($id);
        $invoice = Invoice::where('user_id', auth()->user()->parent)
            ->where('id', $id)
            ->where('open', true)
            ->where('status', true)
            ->with([
                'guests' => function ($query) {
                    $query->select(Fields::get('guests'))
                        ->withPivot('main');
                },
                'company' => function ($query) {
                    $query->select(['id', 'name', 'tin']);
                },
                'rooms' => function ($query) {
                    $query->select(Fields::parsed('rooms'));
                },
                'rooms.guests' => function ($query) use ($id) {
                    $query->select(Fields::get('guests'))
                        ->wherePivot('invoice_id', $id);
                },
                'rooms.guests.parent' => function ($query) {
                    $query->select('id', 'name', 'last_name');
                },
                'rooms.guests.identificationType' => function ($query) {
                    $query->select('id', 'type');
                },
            ])->first(Fields::parsed('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        $customer = null;
        if (!$invoice->guests->isEmpty()) {
            $customer = $invoice->guests->first(function ($guest, $index) {
                return $guest->pivot->main == true;
            });
        }
        
        // dd($invoice, $customer);

        return view('app.invoices.show', compact('invoice', 'customer'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $invoice = Invoice::where('user_id', auth()->user()->parent)
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->first(Fields::get('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        if ($invoice->open) {
            $invoice->delete();

            flash(trans('common.successful'))->success();

            return redirect()->route('invoices.index');
        }

        flash(trans('common.error'))->error();
        
        return back();
    }

    /**
     * Show the form for adding rooms to invoice.
     * 
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function rooms($id = '')
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
            ->where('status', '1') # It is free
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
    public function addRooms(AddRooms $request, $id)
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
    
                if (!empty($request->get('end', null))) {
                    $start = Carbon::createFromFormat('Y-m-d', $request->start);
                    $end = Carbon::createFromFormat('Y-m-d', $request->end);
                    $quantity = $start->diffInDays($end);
                } else {
                    $quantity = 1;
                }
                
                // TODO: Crear procedimiento para incrementar el valor diario para END null

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
        $id = Id::get($id);
        $invoice = Invoice::where('user_id', auth()->user()->parent)
            ->where('id', $id)
            ->where('open', true)
            ->where('status', true)
            ->with([
                'rooms' => function ($query) {
                    $query->select('id', 'number');
                },
                'rooms.guests' => function ($query) use ($id) {
                    $query->select('id', 'name', 'last_name')
                        ->wherePivot('invoice_id', $id);
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
    public function storeGuests(StoreInvoiceGuest $request, $id)
    {
        $invoice = Invoice::where('user_id', auth()->user()->parent)
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->with([
                'guests' => function ($query) {
                    $query->select('id');
                },
            ])->first(['id']);

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
        $guest->status = true; # In hotel
        $guest->identificationType()->associate(Id::get($request->type));
        $guest->user()->associate(auth()->user()->parent);

        $isMinor = $this->isMinor($request->get('birthdate', null));
        $responsible = Id::get($request->get('responsible_adult', null));

        if ($isMinor and !empty($responsible)) {
            $guest->responsible_adult = $responsible;
        }

        if ($guest->save()) {
            $main = $invoice->guests->isEmpty() ? true : false;
            $invoice->guests()->attach($guest->id, ['main' => $main]);

            $guest->rooms()->attach(Id::get($request->room), [
                'invoice_id' => $invoice->id
            ]);

            flash(trans('common.successful'))->success();

            return back();
        }

        flash(trans('common.error'))->error();

        return back();
    }

    /**
     * Show form to add guests to invoice.
     *
     * @param $id
     * @param $guest
     * @return \Illuminate\Http\Response
     */
    public function guests($id, $guest)
    {
        $id = Id::get($id);
        $invoice = Invoice::where('user_id', auth()->user()->parent)
            ->where('id', $id)
            ->where('open', true)
            ->where('status', true)
            ->with([
                'rooms' => function ($query) {
                    $query->select('id', 'number');
                },
                'rooms.guests' => function ($query) use ($id) {
                    $query->select('id', 'name', 'last_name')
                        ->wherePivot('invoice_id', $id);
                }
            ])->first(Fields::parsed('invoices'));

        $guest = Guest::where('id', Id::get($guest))
            ->where('status', false) // Not in hotel
            ->with([
                'identificationType' => function ($query) {
                    $query->select('id', 'type');
                },
            ])->first(Fields::get('guests'));

        if (empty($invoice) or empty($guest)) {
            abort(404);
        }

        return view('app.invoices.add-guests', compact('invoice', 'guest'));
    }

    /**
     * Add guests to invoice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function addGuests(Request $request, $id)
    {
        $invoice = Invoice::where('user_id', auth()->user()->parent)
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->with([
                'guests' => function ($query) {
                    $query->select('id');
                },
            ])->first(['id']);

        $guest = Guest::where('id', Id::get($request->guest))
            ->where('status', false) // Not in hotel
            ->first(['id', 'birthdate']);
        
        if (empty($invoice) or empty($guest)) {
            abort(404);
        }

        $responsible = Id::get($request->get('responsible_adult', null));

        if ($this->isMinor($guest->birthdate) and !empty($responsible)) {
            $guest->responsible_adult = $responsible;
            $guest->update();
        }

        $main = $invoice->guests->isEmpty() ? true : false;
        $invoice->guests()->attach($guest->id, ['main' => $main]);

        $guest->rooms()->attach(Id::get($request->room), [
            'invoice_id' => $invoice->id
        ]);

        flash(trans('common.successful'))->success();

        return redirect()->route('invoices.guests.search', [
            'id' => Hashids::encode($invoice->id)
        ]);
    }

    /**
     * Check if the guest is a minor.
     *
     * @param string $birthdate
     * @return boolean
     */
    private function isMinor($birthdate = '')
    {
        if (empty($birthdate)) {
            return false;
        }

        $age = Age::get($birthdate);

        // TODO: Crear tabla de configuraciones
        // Agregar edad limite para ser adulto
        // Agregar Hora hotele
        if ($age < 18) {
            return true;
        }

        return false;
    }
}
