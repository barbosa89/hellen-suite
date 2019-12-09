<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use App\Helpers\{Age, Customer, Fields, Id, Input, Random};
use App\Welkome\{Additional, Company, Guest, Hotel, Invoice, Product, Room, Service, Vehicle};
use App\Http\Requests\{
    AddGuests,
    AddProducts,
    AddRooms,
    AddServices,
    ChangeGuestRoom,
    Multiple,
    RemoveGuests,
    ChangeRoom,
    StoreAdditional,
    StoreInvoice,
    StoreInvoiceGuest,
    StoreRoute
};
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

// TODO: Crear tabla de configuraciones
// Agregar edad limite para ser adulto
// Agregar Hora hotelera
// Pagos
class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = Invoice::query();
        $query->where('user_id', Id::parent())
            ->where('status', true)
            ->with([
                'hotel' => function ($query) {
                    $query->select(Fields::get('hotels'));
                },
                'guests' => function ($query) {
                    $query->select(Fields::get('guests'))
                        ->wherePivot('main', true)
                        ->withPivot('main');
                },
                'company' => function ($query) {
                    $query->select(Fields::get('companies'));
                },
                'payments' => function ($query) {
                    $query->select(Fields::get('payments'));
                }
            ]);

        if (auth()->user()->hasRole('receptionist')) {
            $query->where('hotel_id', auth()->user()->headquarters()->first()->id);
        }

        $invoices = $query->get(Fields::get('invoices'));
        $invoices = $invoices->sortByDesc('created_at');

        return view('app.invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $hotel = Hotel::where('user_id', Id::parent())
            ->where('id', Id::get(session('hotel')))
            ->with([
                'rooms' => function ($query)
                {
                    $ids = explode(',', session('rooms'));
                    $query->whereIn('id', Id::pool($ids))
                        ->select(Fields::get('rooms'));
                }
            ])->first(Fields::get('hotels'));


        return view('app.invoices.create', compact('hotel'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreInvoice $request)
    {
        $status = false;
        $numbers = collect($request->room);
        $invoiceId = null;

        DB::transaction(function () use (&$status, &$invoiceId, $request, $numbers) {
            try {
                $invoice = $this->new();
                $invoice->origin = $request->get('origin', null);
                $invoice->destination = $request->get('destination', null);
                $invoice->hotel()->associate(Id::get($request->hotel));

                if ($request->registry == 'reservation') {
                    $invoice->reservation = true;
                }

                $rooms = Room::where('user_id', Id::parent())
                    ->whereIn('number', $numbers->pluck('number')->toArray())
                    ->where('hotel_id', Id::get($request->hotel))
                    ->where('status', '1')
                    ->get(Fields::get('rooms'));

                foreach ($rooms as $room) {
                    $selected = $numbers->where('number', $room->number)->first();
                    $start = Carbon::createFromFormat('Y-m-d', $selected['start']);

                    if (empty($selected['end'])) {
                        $end = $start->copy()->addDay();
                    } else {
                        $end = Carbon::createFromFormat('Y-m-d', $selected['end']);
                    }

                    $quantity = $start->diffInDays($end);
                    $discount = ($room->price - $selected['price']) * $quantity;
                    $taxes = ($selected['price'] * $room->tax) * $quantity;
                    $subvalue = $selected['price'] * $quantity;

                    $attach[$room->id] = [
                        'quantity' => $quantity,
                        'discount' => $discount,
                        'subvalue' => $subvalue,
                        'taxes' => $taxes,
                        'value' => $subvalue + $taxes,
                        'start' => $start->toDateString(),
                        'end' => $end->toDateString()
                    ];
                }

                foreach ($attach as $key => $item) {
                    $invoice->discount += $item['discount'];
                    $invoice->subvalue += $item['subvalue'];
                    $invoice->taxes += $item['taxes'];
                    $invoice->value += $item['value'];
                }
                // TODO: Crear procedimiento para incrementar el valor diario para END null
                if ($invoice->save()) {
                    Room::where('user_id', Id::parent())
                        ->whereIn('number', $numbers->pluck('number')->toArray())
                        ->where('hotel_id', Id::get($request->hotel))
                        ->update(['status' => '0']);

                    $invoice->rooms()->sync($attach);
                    $invoiceId = $invoice->id;
                    $status = true;

                    session()->forget('hotel');
                    session()->forget('rooms');
                }
            } catch (\Throwable $e) {
                Storage::append('invoice.log', $e->getMessage());
            }
        });

        if ($status) {
            flash(trans('common.successful'))->success();

            return redirect()->route('invoices.guests.search', [
                'id' => Hashids::encode($invoiceId)
            ]);
        }

        flash(trans('common.error'))->error();

        return redirect()->route('invoices.index');
    }

    /**
     * Return a newly Invoice instance.
     *
     * @return \App\Welkome\Invoice
     */
    private function new()
    {
        $invoice = new Invoice();
        $invoice->number = Random::consecutive();
        $invoice->subvalue = 0.0;
        $invoice->taxes = 0.0;
        $invoice->discount = 0.0;
        $invoice->value = 0.0;
        $invoice->status = true;
        $invoice->user()->associate(Id::parent());

        return $invoice;
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
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', $id)
            ->where('status', true)
            ->first(Fields::parsed('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        $invoice->load([
            'guests' => function ($query) {
                $query->select(Fields::get('guests'))
                    ->withPivot('main');
            },
            'guests.vehicles' => function ($query) use ($id) {
                $query->select(Fields::parsed('vehicles'))
                    ->wherePivot('invoice_id', $id);
            },
            'company' => function ($query) {
                $query->select(Fields::get('companies'));
            },
            'rooms' => function ($query) {
                $query->select(Fields::parsed('rooms'))
                    ->withPivot('quantity', 'discount', 'subvalue', 'taxes', 'value', 'start', 'end');
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
            'products' => function ($query) {
                $query->select(Fields::parsed('products'))
                    ->withPivot('id', 'quantity', 'value', 'created_at');
            },
            'services' => function ($query) {
                $query->select(Fields::parsed('services'))
                    ->withPivot('id', 'quantity', 'value', 'created_at');
            },
            'additionals' => function ($query) {
                $query->select(['id', 'description', 'billable','value', 'invoice_id', 'created_at']);
            },
            'payments' => function ($query)
            {
                $query->select(Fields::get('payments'));
            }
        ]);

        $customer = Customer::get($invoice);

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
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->with([
                'rooms' => function ($query) {
                    $query->select(Fields::get('rooms'));
                },
                'guests' => function ($query) {
                    $query->select(Fields::get('guests'));
                },
                'products' => function ($query) {
                    $query->select(Fields::parsed('products'))
                        ->withPivot('id', 'quantity', 'value', 'created_at');
                }
            ])->first(Fields::get('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        $status = false;

        DB::transaction(function () use (&$status, &$invoice) {
            try {
                // Change Room status to cleaning
                Room::whereIn('id', $invoice->rooms->pluck('id')->toArray())->update(['status' => '2']);

                // Change Guest status to false: Guest is not in hotel
                if ($invoice->guests->isNotEmpty()) {
                    Guest::whereIn('id', $invoice->guests->pluck('id')->toArray())->update(['status' => false]);
                }

                // Restore product stocks
                if ($invoice->products->isNotEmpty()) {
                    $invoice->products->each(function ($product)
                    {
                        $product->quantity += $product->pivot->quantity;
                        $product->save();
                    });
                }

                $invoice->delete();
                $status = true;
            } catch (\Throwable $e) {
                Storage::append('invoice.log', $e->getMessage());
            }
        });

        if ($status) {
            flash(trans('common.successful'))->success();

            return redirect()->route('invoices.index');
        }

        flash(trans('common.error'))->error();

        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $query = Input::clean($request->get('query', null));

        if (empty($query)) {
            return back();
        }

        $invoices = Invoice::where('user_id', Id::parent())
            ->whereLike([
                'number',
                'guests.name',
                'guests.last_name',
                'guests.dni',
                'company.business_name',
                'hotel.business_name'
            ], $query)->paginate(
                config('welkome.paginate'),
                Fields::get('invoices')
            );

        return view('app.invoices.search', compact('invoices', 'query'));
    }

    /**
     * Show the form for adding rooms to invoice.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function rooms($id = '')
    {
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->with([
                'hotel' => function ($query) {
                    $query->select(Fields::get('hotels'));
                },
                'guests' => function ($query) {
                    $query->select(Fields::get('guests'))
                        ->withPivot('main');
                },
                'company' => function ($query) {
                    $query->select(Fields::get('companies'));
                },
                'payments' => function ($query)
                {
                    $query->select(Fields::get('payments'));
                }
            ])->first(Fields::get('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        $rooms = Room::where('hotel_id', $invoice->hotel->id)
            ->where('status', '1') // It is free
            ->get(Fields::get('rooms'));

        if ($rooms->isEmpty()) {
            flash('No hay habitaciones disponibles')->info();

            return redirect()->route('invoices.show', [
                'id' => Hashids::encode($invoice->id)
            ]);
        }

        $customer = Customer::get($invoice);

        return view('app.invoices.add-rooms', compact('invoice', 'rooms', 'customer'));
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

        DB::transaction(function () use (&$status, $request, $id) {
            try {
                $invoice = Invoice::where('user_id', Id::parent())
                    ->where('id', Id::get($id))
                    ->where('open', true)
                    ->where('status', true)
                    ->with([
                        'hotel' => function ($query) {
                            $query->select(Fields::get('hotels'));
                        }
                    ])->first(Fields::parsed('invoices'));

                $room = Room::where('user_id', Id::parent())
                    ->where('number', $request->number)
                    ->where('hotel_id', $invoice->hotel->id)
                    ->where('status', '1')
                    ->first(Fields::get('rooms'));

                $start = Carbon::createFromFormat('Y-m-d', $request->start);

                if (empty($request->end)) {
                    $end = $start->copy()->addDay();
                } else {
                    $end = Carbon::createFromFormat('Y-m-d', $request->end);
                }

                $quantity = $start->diffInDays($end);
                $discount = ($room->price - $request->price) * $quantity;
                $taxes = ($request->price * $room->tax) * $quantity;
                $subvalue = $request->price * $quantity;

                $invoice->rooms()->attach(
                    $room->id,
                    [
                        'quantity' => $quantity,
                        'discount' => $discount,
                        'subvalue' => $subvalue,
                        'taxes' => $taxes,
                        'value' => $subvalue + $taxes,
                        'start' => $request->start,
                        'end' => $end->toDateString()
                    ]
                );

                $invoice->discount += $discount;
                $invoice->subvalue += $subvalue;
                $invoice->taxes += $taxes;
                $invoice->value += $subvalue + $taxes;
                $invoice->save();

                $room->status = '0';
                $room->save();
                $status = true;
            } catch (\Throwable $e) {
                Storage::append('invoice.log', $e->getMessage());
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
     * Redirect to create new invoice with many rooms.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function multiple(Multiple $request)
    {
        $rooms = collect($request->rooms);

        session()->put('hotel', $request->hotel);
        session()->put('rooms', $rooms->implode('hash', ','));

        return response()->json([
            'redirect' => '/invoices/create'
        ]);
    }

    /**
     * Show the form to change a room from the invoice.
     *
     * @param int $id
     * @param int $room
     * @return \Illuminate\Http\Response
     */
    public function showFormToChangeRoom($id, $room)
    {
        $id = Id::get($id);
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', $id)
            ->where('open', true)
            ->where('status', true)
            ->first(Fields::parsed('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        $invoice->load([
            'hotel' => function ($query) {
                $query->select(Fields::get('hotels'));
            },
            'guests' => function ($query) {
                $query->select(Fields::get('guests'))
                    ->withPivot('main');
            },
            'company' => function ($query) {
                $query->select(Fields::get('companies'));
            },
            'rooms' => function ($query) {
                $query->select(Fields::parsed('rooms'))
                    ->withPivot('quantity', 'discount', 'subvalue', 'taxes', 'value', 'start', 'end');
            },
            'rooms.guests' => function ($query) use ($id) {
                $query->select(Fields::get('guests'))
                    ->wherePivot('invoice_id', $id);
            },
            'payments' => function ($query)
            {
                $query->select(Fields::get('payments'));
            }
        ]);

        $room = $invoice->rooms->where('id', Id::get($room))
            ->where('hotel_id', $invoice->hotel->id)
            ->first();

        $rooms = Room::where('hotel_id', $invoice->hotel->id)
            ->where('status', '1') // It is free
            ->get(Fields::get('rooms'));

        if ($rooms->isEmpty()) {
            flash('No hay habitaciones disponibles')->info();

            return redirect()->route('invoices.show', [
                'id' => Hashids::encode($invoice->id)
            ]);
        }

        $customer = Customer::get($invoice);

        return view('app.invoices.change-room', compact('invoice', 'rooms', 'customer', 'room'));
    }

    // TODO: Procedimiento para actualizar quitar responsable a quienes hayan pasado el mínimo de edad
    /**
     * Change a room in the invoice with relationships.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function changeRoom(ChangeRoom $request, $id, $roomId)
    {
        $status = false;

        DB::transaction(function () use (&$status, $request, $id, $roomId) {
            try {
                $id = Id::get($id);
                $invoice = Invoice::where('user_id', Id::parent())
                    ->where('id', $id)
                    ->where('open', true)
                    ->where('status', true)
                    ->first(Fields::parsed('invoices'));

                if (empty($invoice)) {
                    abort(404);
                }

                $invoice->load([
                    'hotel' => function ($query) {
                        $query->select(Fields::get('hotels'));
                    },
                    'guests' => function ($query) {
                        $query->select(Fields::get('guests'))
                            ->withPivot('main');
                    },
                    'company' => function ($query) {
                        $query->select(Fields::get('companies'));
                    },
                    'rooms' => function ($query) {
                        $query->select(Fields::parsed('rooms'))
                            ->withPivot('quantity', 'discount', 'subvalue', 'taxes', 'value', 'start', 'end');
                    },
                    'rooms.guests' => function ($query) use ($id) {
                        $query->select(Fields::get('guests'))
                            ->wherePivot('invoice_id', $id);
                    }
                ]);

                // The room to change from the invoice
                $current = $invoice->rooms->where('id', Id::get($roomId))
                    ->where('hotel_id', $invoice->hotel->id)
                    ->first();

                // The new room to add to the invoice
                $room = Room::where('hotel_id', $invoice->hotel->id)
                    ->where('number', $request->number)
                    ->where('status', '1') // It is free
                    ->first(Fields::get('rooms'));

                ### Rooms ###

                // Current values are subtracted
                $invoice->discount -= $current->pivot->discount;
                $invoice->subvalue -= $current->pivot->subvalue;
                $invoice->taxes -= $current->pivot->taxes;
                $invoice->value -= $current->pivot->value;

                // Detach current room
                $invoice->rooms()->detach($current->id);

                // Calculating de new values
                $discount = ($room->price - $request->price) * $current->pivot->quantity;
                $taxes = ($request->price * $room->tax) * $current->pivot->quantity;
                $subvalue = $request->price * $current->pivot->quantity;

                $invoice->rooms()->attach(
                    $room->id,
                    [
                        'quantity' => $current->pivot->quantity, // This is same that before
                        'discount' => $discount,
                        'subvalue' => $subvalue,
                        'taxes' => $taxes,
                        'value' => $subvalue + $taxes,
                        'start' => $current->pivot->start, // This is same that before
                        'end' => $current->pivot->end // This is same that before
                    ]
                );

                // Summing the new values
                $invoice->discount += $discount;
                $invoice->subvalue += $subvalue;
                $invoice->taxes += $taxes;
                $invoice->value += $subvalue + $taxes;
                $invoice->save();

                ### Guests ###

                // Check the room has guests
                if ($current->guests->isNotEmpty()) {
                    foreach ($current->guests as $guest) {
                        // Detach room of the guests
                        $guest->rooms()
                            ->wherePivot('invoice_id', $invoice->id)
                            ->detach($current->id);

                        // Attach the new room of the guests
                        $guest->rooms()->attach($room->id, [
                            'invoice_id' => $invoice->id
                        ]);
                    }
                }

                // Change status of the current room
                $current->status = '2';
                $current->save();

                // Change status of the new room
                $room->status = '0';
                $room->save();

                $status = true;
            } catch (\Throwable $e) {
                Storage::append('invoice.log', $e->getMessage());
            }
        });

        if ($status) {
            flash(trans('common.successful'))->success();
        } else {
            flash(trans('common.error'))->error();
        }

        return redirect()->route('invoices.show', [
            'id' => $id
        ]);
    }

    /**
     * Display a listing of searched records.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function searchGuests($id)
    {
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->with([
                'rooms' => function ($query) {
                    $query->select('id');
                },
                'rooms.guests' => function ($query) {
                    $query->select('id', 'name', 'last_name');
                },
                'guests' => function ($query) {
                    $query->select(Fields::get('guests'))
                        ->withPivot('main');
                },
                'company' => function ($query) {
                    $query->select(Fields::get('companies'));
                },
                'payments' => function ($query)
                {
                    $query->select(Fields::get('payments'));
                }
            ])->first(Fields::parsed('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        if ($invoice->rooms->count() == 0) {
            flash(trans('invoices.firstStep'))->info();

            return redirect()->route('invoices.rooms.add', [
                'id' => Hashids::encode($invoice->id)
            ]);
        }

        $customer = Customer::get($invoice);

        return view('app.invoices.search-guests', compact('invoice', 'customer'));
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
        $invoice = Invoice::where('user_id', Id::parent())
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
                },
                'guests' => function ($query) {
                    $query->select(Fields::get('guests'))
                        ->withPivot('main');
                },
                'company' => function ($query) {
                    $query->select(Fields::get('companies'));
                },
                'payments' => function ($query)
                {
                    $query->select(Fields::get('payments'));
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

        $guests = $this->countGuestsPerRoom($invoice);

        $customer = Customer::get($invoice);

        return view('app.invoices.add-guests', compact('invoice', 'guest', 'guests', 'customer'));
    }

    /**
     * Return number of guests in all rooms.
     *
     * @param  \App\Welkome\Invoice  $invoice
     * @return int
     */
    private function countGuestsPerRoom(Invoice $invoice): int
    {
        $guests = 0;

        $invoice->rooms->each(function ($room) use (&$guests)
        {
            $guests += $room->guests()->count();
        });

        return $guests;
    }

    /**
     * Add guests to invoice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function addGuests(AddGuests $request, $id)
    {
        $invoice = Invoice::where('user_id', Id::parent())
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
            ->first(Fields::get('guests'));

        if (empty($invoice) or empty($guest)) {
            abort(404);
        }

        if ($invoice->guests->where('id', $guest->id)->count() == 0) {
            $responsible = Id::get($request->get('responsible_adult', null));

            if (Customer::isMinor($guest->birthdate) and !empty($responsible)) {
                $guest->responsible_adult = $responsible;
            }

            $invoice->guests()->attach($guest->id, ['main' => $invoice->guests->isEmpty()]);
        }

        $guest->rooms()
            ->wherePivot('invoice_id', $invoice->id)
            ->detach();

        $guest->rooms()->attach(Id::get($request->room), [
            'invoice_id' => $invoice->id
        ]);

        $guest->status = true;
        $guest->save();

        flash(trans('common.successful'))->success();

        return redirect()->route('invoices.guests.search', [
            'id' => Hashids::encode($invoice->id)
        ]);
    }

    /**
     * Remove guests to invoice.
     *
     * @param int $id
     * @param int $guestId
     * @return \Illuminate\Http\Response
     */
    public function removeGuests($id, $guestId)
    {
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->with([
                'guests' => function ($query) {
                    $query->select('id')
                        ->where('responsible_adult', false);
                },
                'guests.rooms' => function ($query) {
                    $query->select('id', 'number');
                },
            ])->first(['id']);

        // Check if the invoice only has a guest
        if ($invoice->guests->count() == 1) {
            flash(trans('invoices.onlyOne'))->error();

            return back();
        }

        // Get the guest to remove from invoice
        $guest = $invoice->guests->where('id', Id::get($guestId))->first();

        // Check if invoice or guest are null
        if (empty($invoice) or empty($guest)) {
            abort(404);
        }

        // Detach the guest from invoice
        $invoice->guests()->detach($guest->id);

        // Refresh the guests relationship to select the main guest
        $invoice->load([
            'guests' => function ($query) {
                $query->select('id')
                    ->where('responsible_adult', false);
            }
        ]);

        // Select the main guest
        $invoice->guests()->updateExistingPivot(
            $invoice->guests->first(),
            ['main' => true]
        );

        // Detach the guest from room
        $guest->rooms()
            ->wherePivot('invoice_id', $invoice->id)
            ->detach($guest->rooms->first()->id);

        // The guest will be available to add to invoice
        $guest->status = false;
        $guest->save();

        flash(trans('common.successful'))->success();

        return redirect()->route('invoices.show', [
            'id' => Hashids::encode($invoice->id)
        ]);
    }

    /**
     * Show the form to change guest room from the invoice.
     *
     * @param int $id
     * @param int $guest
     * @return \Illuminate\Http\Response
     */
    public function showFormToChangeGuestRoom($id, $guest)
    {
        $id = Id::get($id);
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', $id)
            ->where('open', true)
            ->where('status', true)
            ->first(Fields::parsed('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        $invoice->load([
            'hotel' => function ($query) {
                $query->select(Fields::get('hotels'));
            },
            'guests' => function ($query) {
                $query->select(Fields::get('guests'))
                    ->withPivot('main');
            },
            'guests.rooms' => function ($query) use ($id) {
                $query->select(Fields::parsed('rooms'));
            },
            'company' => function ($query) {
                $query->select(Fields::get('companies'));
            },
            'rooms' => function ($query) use ($id) {
                $query->select(Fields::parsed('rooms'));
            },
            'payments' => function ($query)
            {
                $query->select(Fields::get('payments'));
            }
        ]);

        if ($invoice->rooms->count() <= 1) {
            flash('No es posible cambiar al huésped de habitación')->info();

            return redirect()->route('invoices.show', [
                'id' => Hashids::encode($invoice->id)
            ]);
        }

        if ($invoice->guests->count() <= 1) {
            flash('No es posible cambiar al huésped de habitación')->info();

            return redirect()->route('invoices.show', [
                'id' => Hashids::encode($invoice->id)
            ]);
        }

        $guest = $invoice->guests->where('id', Id::get($guest))->first();
        $room = $guest->rooms->first();
        $customer = Customer::get($invoice);

        return view('app.invoices.change-guest-room', compact('invoice', 'customer', 'guest', 'room'));
    }

    /**
     * Remove guests to invoice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id
     * @param int $guest
     * @return \Illuminate\Http\Response
     */
    public function changeGuestRoom(ChangeGuestRoom $request, $id, $guest)
    {
        $id = Id::get($id);
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', $id)
            ->where('open', true)
            ->where('status', true)
            ->first(Fields::parsed('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        $invoice->load([
            'hotel' => function ($query) {
                $query->select(Fields::get('hotels'));
            },
            'guests' => function ($query) {
                $query->select(Fields::get('guests'))
                    ->withPivot('main');
            },
            'guests.rooms' => function ($query) use ($id) {
                $query->select(Fields::parsed('rooms'));
            },
            'company' => function ($query) {
                $query->select(Fields::get('companies'));
            },
            'rooms' => function ($query) use ($id) {
                $query->select(Fields::parsed('rooms'));
            }
        ]);

        if ($invoice->rooms->count() <= 1) {
            flash('No es posible cambiar al huésped de habitación')->info();

            return redirect()->route('invoices.show', [
                'id' => Hashids::encode($invoice->id)
            ]);
        }

        if ($invoice->guests->count() <= 1) {
            flash('No es posible cambiar al huésped de habitación')->info();

            return redirect()->route('invoices.show', [
                'id' => Hashids::encode($invoice->id)
            ]);
        }

        // The guest
        $guest = $invoice->guests->where('id', Id::get($guest))->first();

        // Detach current guest room
        $guest->rooms()->detach($guest->rooms()->first()->id);

        // The room to assign to guest
        $room = $invoice->rooms->where('number', $request->number)->first();

        // Attach the selected room to guest
        $guest->rooms()->attach($room->id, [
            'invoice_id' => $invoice->id
        ]);

        flash(trans('common.successful'))->success();

        return redirect()->route('invoices.show', [
            'id' => Hashids::encode($invoice->id)
        ]);
    }

    /**
     * Show the form for adding products to invoice.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function products($id = '')
    {
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->where('reservation', false)
            ->with([
                'rooms' => function ($query) {
                    $query->select('id', 'number');
                },
                'guests' => function ($query) {
                    $query->select(Fields::get('guests'))
                        ->withPivot('main');
                },
                'company' => function ($query) {
                    $query->select(Fields::get('companies'));
                },
                'hotel' => function ($query) {
                    $query->select(Fields::get('hotels'));
                },
                'payments' => function ($query)
                {
                    $query->select(Fields::get('payments'));
                }
            ])->first(Fields::get('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        $products = Product::where('user_id', Id::parent())
            ->where('hotel_id', $invoice->hotel->id)
            ->where('quantity', '>', 0)
            ->where('status', true)
            ->get(Fields::get('products'));

        if ($products->isEmpty()) {
            flash('No hay productos disponibles')->info();

            return redirect()->route('invoices.show', [
                'id' => Hashids::encode($invoice->id)
            ]);
        }

        $customer = Customer::get($invoice);

        return view('app.invoices.add-products', compact('invoice', 'products', 'customer'));
    }

    /**
     * Store the product values to invoice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function addProducts(AddProducts $request, $id)
    {
        // TODO: Implementar el descuento (productos vendidos) de productos por habitación
        $status = false;

        DB::transaction(function () use (&$status, $request, $id) {
            try {
                $invoice = Invoice::where('user_id', Id::parent())
                    ->where('id', Id::get($id))
                    ->where('open', true)
                    ->where('status', true)
                    ->where('reservation', false)
                    ->with([
                        'hotel' => function ($query) {
                            $query->select(Fields::get('hotels'));
                        }
                    ])->first(Fields::parsed('invoices'));

                $product = Product::where('user_id', Id::parent())
                    ->where('id', Id::get($request->product))
                    ->where('hotel_id', $invoice->hotel->id)
                    ->where('quantity', '>', 0)
                    ->where('status', true)
                    ->first(Fields::get('products'));

                $value = (int) $request->quantity * $product->price;

                $invoice->products()->attach(
                    $product->id,
                    [
                        'quantity' => $request->quantity,
                        'value' => $value,
                        'created_at' => Carbon::now()->toDateTimeString()
                    ]
                );

                $invoice->subvalue += $value;
                $invoice->value += $value;
                $invoice->save();

                $product->quantity -= $request->quantity;
                $product->save();

                $status = true;
            } catch (\Throwable $e) {
                Storage::append('invoice.log', $e->getMessage());
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
     * Remove a product from the invoice.
     *
     * @param int $id
     * @param int $record
     * @return \Illuminate\Http\Response
     */
    public function removeProduct($id, $record)
    {
        $status = false;

        DB::transaction(function () use (&$status, $id, $record) {
            try {
                $invoice = Invoice::where('user_id', Id::parent())
                    ->where('id', Id::get($id))
                    ->where('open', true)
                    ->where('status', true)
                    ->where('reservation', false)
                    ->with([
                        'products' => function ($query) use ($record) {
                            $query->select(Fields::parsed('products'))
                                ->wherePivot('id', Id::get($record))
                                ->withPivot('id', 'quantity', 'value', 'created_at');
                        }
                    ])->first(Fields::parsed('invoices'));

                $product = $invoice->products->first();

                $product->quantity += $product->pivot->quantity;
                $product->save();

                $invoice->subvalue -= $product->pivot->value;
                $invoice->value -= $product->pivot->value;
                $invoice->save();

                $invoice->products()
                    ->wherePivot('id', Id::get($record))
                    ->detach($product->id);

                $status = true;
            } catch (\Throwable $e) {
                Storage::append('invoice.log', $e->getMessage());
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
     * Show the form for adding services to invoice.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function services($id = '')
    {
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->where('reservation', false)
            ->with([
                'guests' => function ($query) {
                    $query->select(Fields::get('guests'))
                        ->withPivot('main');
                },
                'company' => function ($query) {
                    $query->select(Fields::get('companies'));
                },
                'hotel' => function ($query) {
                    $query->select(Fields::get('hotels'));
                },
                'payments' => function ($query)
                {
                    $query->select(Fields::get('payments'));
                }
            ])
            ->first(Fields::get('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        $services = Service::where('user_id', Id::parent())
            ->where('hotel_id', $invoice->hotel->id)
            ->whereStatus(true)
            ->get(Fields::get('services'));

        if ($services->isEmpty()) {
            flash('No hay servicios disponibles')->info();

            return redirect()->route('invoices.show', [
                'id' => Hashids::encode($invoice->id)
            ]);
        }

        $customer = Customer::get($invoice);

        return view('app.invoices.add-services', compact('invoice', 'services', 'customer'));
    }

    /**
     * Store the services values to invoice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function addServices(AddServices $request, $id)
    {
        $status = false;

        DB::transaction(function () use (&$status, $request, $id) {
            try {
                $invoice = Invoice::where('user_id', Id::parent())
                    ->where('id', Id::get($id))
                    ->where('open', true)
                    ->where('status', true)
                    ->where('reservation', false)
                    ->with([
                        'hotel' => function ($query) {
                            $query->select(Fields::get('hotels'));
                        }
                    ])->first(Fields::parsed('invoices'));

                $service = Service::where('user_id', Id::parent())
                    ->where('id', Id::get($request->service))
                    ->where('hotel_id', $invoice->hotel->id)
                    ->whereStatus(true)
                    ->first(Fields::get('services'));

                $value = (int) $request->quantity * $service->price;

                $invoice->services()->attach(
                    $service->id,
                    [
                        'quantity' => $request->quantity,
                        'value' => $value,
                        'created_at' => Carbon::now()->toDateTimeString()
                    ]
                );

                $invoice->subvalue += $value;
                $invoice->value += $value;
                $invoice->update();

                $status = true;
            } catch (\Throwable $e) {
                Storage::append('invoice.log', $e->getMessage());
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
     * Remove a service from the invoice.
     *
     * @param int $id
     * @param int $record
     * @return \Illuminate\Http\Response
     */
    public function removeService($id, $record)
    {
        $status = false;

        DB::transaction(function () use (&$status, $id, $record) {
            try {
                $invoice = Invoice::where('user_id', Id::parent())
                    ->where('id', Id::get($id))
                    ->where('open', true)
                    ->where('status', true)
                    ->where('reservation', false)
                    ->with([
                        'services' => function ($query) use ($record) {
                            $query->select(Fields::parsed('services'))
                                ->wherePivot('id', Id::get($record))
                                ->withPivot('id', 'quantity', 'value', 'created_at');
                        }
                    ])->first(Fields::parsed('invoices'));

                $service = $invoice->services->first();

                $invoice->subvalue -= $service->pivot->value;
                $invoice->value -= $service->pivot->value;
                $invoice->save();

                $invoice->services()
                    ->wherePivot('id', Id::get($record))
                    ->detach($service->id);

                $status = true;
            } catch (\Throwable $e) {
                Storage::append('invoice.log', $e->getMessage());
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
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function searchCompanies($id)
    {
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->with([
                'guests' => function ($query) {
                    $query->select(Fields::get('guests'))
                        ->withPivot('main');
                },
                'company' => function ($query) {
                    $query->select(Fields::get('companies'));
                },
                'payments' => function ($query)
                {
                    $query->select(Fields::get('payments'));
                }
            ])
            ->first(Fields::parsed('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        $customer = Customer::get($invoice);

        return view('app.invoices.search-companies', compact('invoice', 'customer'));
    }

    /**
     * Attach a company to invoice.
     *
     * @param string $id
     * @param string $company
     * @return \Illuminate\Http\Response
     */
    public function addCompanies($id, $company)
    {
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->first(['id']);

        $company = Company::where('user_id', Id::parent())
            ->where('id', Id::get($company))
            ->first(Fields::get('companies'));

        if (empty($invoice) or empty($company)) {
            abort(404);
        }

        $invoice->company()->associate($company->id);

        if ($invoice->update()) {
            flash(trans('common.successful'))->success();

            return redirect()->route('invoices.show', [
                'id' => $id
            ]);
        }

        flash(trans('common.error'))->error();

        return redirect()->route('invoices.show', [
            'id' => $id
        ]);
    }

    /**
     * Detach a company from invoice.
     *
     * @param string $id
     * @param string $company
     * @return \Illuminate\Http\Response
     */
    public function removeCompany($id, $company)
    {
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->with([
                'company' => function ($query) use ($company)
                {
                    $query->select(Fields::get('companies'))
                        ->where('id', Id::get($company));
                }
            ])->first(['id', 'company_id']);

        if (empty($invoice) or empty($invoice->company)) {
            abort(404);
        }

        $invoice->company()->dissociate();

        if ($invoice->update()) {
            flash(trans('common.successful'))->success();

            return redirect()->route('invoices.show', [
                'id' => $id
            ]);
        }

        flash(trans('common.error'))->error();

        return redirect()->route('invoices.show', [
            'id' => $id
        ]);
    }

    /**
     * Display a listing of searched records from vehicle module.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function searchVehicles($id)
    {
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->where('reservation', false)
            ->with([
                'rooms' => function ($query) {
                    $query->select('id');
                },
                'rooms.guests' => function ($query) {
                    $query->select('id', 'name', 'last_name');
                },
                'guests' => function ($query) {
                    $query->select(Fields::get('guests'))
                        ->withPivot('main');
                },
                'company' => function ($query) {
                    $query->select(Fields::get('companies'));
                },
                'payments' => function ($query)
                {
                    $query->select(Fields::get('payments'));
                }
            ])->first(Fields::parsed('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        if ($invoice->rooms->isEmpty()) {
            flash(trans('invoices.firstStep'))->info();

            return redirect()->route('invoices.rooms.add', [
                'id' => Hashids::encode($invoice->id)
            ]);
        }

        if ($invoice->guests->isEmpty()) {
            flash(trans('invoices.withoutGuests'))->info();

            return redirect()->route('invoices.show', [
                'id' => Hashids::encode($invoice->id)
            ]);
        }

        $customer = Customer::get($invoice);

        return view('app.invoices.search-vehicles', compact('invoice', 'customer'));
    }

    /**
     * Attach a vehicle to guest invoice.
     *
     * @param string $id
     * @param string $vehicleId
     * @param string $guestId
     * @return \Illuminate\Http\Response
     */
    public function addVehicle($id, $vehicleId, $guestId)
    {
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->where('reservation', false)
            ->with([
                'guests' => function ($query) {
                    $query->select(Fields::get('guests'))
                        ->withPivot('main');
                },
                'guests.vehicles' => function ($query) use ($id) {
                    $query->select(Fields::parsed('vehicles'))
                        ->wherePivot('invoice_id', Id::get($id));
                }
            ])->first(Fields::get('invoices'));

        $vehicle = Vehicle::where('user_id', Id::parent())
            ->where('id', Id::get($vehicleId))
            ->first(Fields::get('vehicles'));

        if (empty($invoice) or empty($vehicle)) {
            abort(404);
        }

        if ($invoice->guests->where('id', Id::get($guestId))->first()->vehicles->isNotEmpty()) {
            flash(trans('invoices.hasVehicles'))->error();

            return redirect()->route('invoices.vehicles.search', [
                'id' => Hashids::encode($invoice->id)
            ]);
        }

        $existingVehicle = null;
        foreach ($invoice->guests as $guest) {
            if ($guest->vehicles->where('id', Id::get($vehicleId))->count() == 1) {
                $existingVehicle = $guest->vehicles->where('id', Id::get($vehicleId))->first();
            }
        }

        if (empty($existingVehicle)) {
            $vehicle->guests()->attach($invoice->guests->where('id', Id::get($guestId))->first()->id, [
                'invoice_id' => $invoice->id,
                'created_at' => Carbon::now()->toDateTimeString()
            ]);

            flash(trans('common.successful'))->success();

            return redirect()->route('invoices.show', [
                'id' => Hashids::encode($invoice->id)
            ]);
        }

        flash(trans('invoices.vehicleAttached'))->error();

        return redirect()->route('invoices.vehicles.search', [
            'id' => Hashids::encode($invoice->id)
        ]);
    }

    /**
     * Detach a vehicle from guest invoice.
     *
     * @param string $id
     * @param string $vehicle
     * @param string $guest
     * @return \Illuminate\Http\Response
     */
    public function removeVehicle($id, $vehicle, $guest)
    {
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->where('reservation', false)
            ->with([
                'guests' => function ($query) use ($guest) {
                    $query->select(Fields::get('guests'))
                        ->where('id', Id::get($guest));
                }
            ])->first(Fields::get('invoices'));

        $vehicle = Vehicle::where('user_id', Id::parent())
            ->where('id', Id::get($vehicle))
            ->first(Fields::get('vehicles'));

        if (empty($invoice) or empty($vehicle)) {
            abort(404);
        }

        $vehicle->guests()
            ->wherePivot('invoice_id', $invoice->id)
            ->detach($invoice->guests->first()->id);

        flash(trans('common.successful'))->success();

        return redirect()->route('invoices.show', [
            'id' => Hashids::encode($invoice->id)
        ]);
    }

    /**
     * Show form to create additional value to the invoice.
     *
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function createAdditional($id)
    {
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->where('reservation', false)
            ->with([
                'rooms' => function ($query) {
                    $query->select('id');
                },
                'guests' => function ($query) {
                    $query->select(Fields::get('guests'))
                        ->withPivot('main');
                },
                'company' => function ($query) {
                    $query->select(Fields::get('companies'));
                },
                'payments' => function ($query)
                {
                    $query->select(Fields::get('payments'));
                }
            ])->first(Fields::parsed('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        $customer = Customer::get($invoice);

        return view('app.invoices.add-additional', compact('invoice', 'customer'));
    }

    /**
     * Store additional value to the invoice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function storeAdditional(StoreAdditional $request, $id)
    {
        $status = false;

        DB::transaction(function () use (&$status, $id, $request) {
            try {
                $invoice = Invoice::where('user_id', Id::parent())
                    ->where('id', Id::get($id))
                    ->where('open', true)
                    ->where('status', true)
                    ->where('reservation', false)
                    ->first(Fields::parsed('invoices'));

                // Create new additional for the invoice
                $additional = new Additional();
                $additional->description = $request->description;
                $additional->value = (float) $request->value;
                $additional->billable = true;
                $additional->invoice()->associate($invoice->id);
                $additional->save();

                // Increment invoice values
                $invoice->subvalue += $additional->value;
                $invoice->value += $additional->value;
                $invoice->save();

                $status = true;
            } catch (\Throwable $e) {
                Storage::append('invoice.log', $e->getMessage());
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
     * Remove additional value to the invoice.
     *
     * @param  string  $id
     * @param  string  $additional
     * @return \Illuminate\Http\Response
     */
    public function destroyAdditional($id, $additional)
    {
        $status = false;

        DB::transaction(function () use (&$status, $id, $additional) {
            try {
                $invoice = Invoice::where('user_id', Id::parent())
                    ->where('id', Id::get($id))
                    ->where('open', true)
                    ->where('status', true)
                    ->where('reservation', false)
                    ->first(Fields::parsed('invoices'));

                if (empty($invoice)) {
                    abort(404);
                }

                // Query the additional to remove
                $additional = Additional::where('invoice_id', $invoice->id)
                    ->where('id', Id::get($additional))
                    ->first(['id', 'value', 'billable','invoice_id']);

                // Check is a billable additional
                if ($additional->billable) {
                    // Subtract the additional values from the invoice
                    $invoice->subvalue -= $additional->value;
                    $invoice->value -= $additional->value;
                    $invoice->save();
                }

                // Destroy the additional
                $additional->delete();

                $status = true;
            } catch (\Throwable $e) {
                Storage::append('invoice.log', $e->getMessage());
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
     * Show form to add external service to the invoice.
     *
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function addExternalService($id)
    {
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->where('reservation', false)
            ->with([
                'rooms' => function ($query) {
                    $query->select('id');
                },
                'guests' => function ($query) {
                    $query->select(Fields::get('guests'))
                        ->withPivot('main');
                },
                'company' => function ($query) {
                    $query->select(Fields::get('companies'));
                },
                'payments' => function ($query)
                {
                    $query->select(Fields::get('payments'));
                }
            ])->first(Fields::parsed('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        $customer = Customer::get($invoice);

        return view('app.invoices.add-external', compact('invoice', 'customer'));
    }

    /**
     * Store additional value to the invoice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function storeExternalService(StoreAdditional $request, $id)
    {
        $status = false;

        DB::transaction(function () use (&$status, $id, $request) {
            try {
                $invoice = Invoice::where('user_id', Id::parent())
                    ->where('id', Id::get($id))
                    ->where('open', true)
                    ->where('status', true)
                    ->where('reservation', false)
                    ->first(Fields::parsed('invoices'));

                // Create new additional for the invoice
                $additional = new Additional();
                $additional->description = $request->description;
                $additional->value = (float) $request->value;
                $additional->billable = false;
                $additional->invoice()->associate($invoice->id);
                $additional->save();

                $status = true;
            } catch (\Throwable $e) {
                Storage::append('invoice.log', $e->getMessage());
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
     * Close an open invoice.
     * The open status is by default in true value.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function close($id)
    {
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->with([
                'rooms' => function ($query) {
                    $query->select(Fields::get('rooms'));
                },
                'guests' => function ($query) {
                    $query->select(Fields::get('guests'));
                }
            ])->first(Fields::get('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        $status = false;

        DB::transaction(function () use (&$status, &$invoice) {
            try {
                $invoice->open = false;

                if ($invoice->save()) {
                    // Change Room status to cleaning
                    Room::whereIn('id', $invoice->rooms->pluck('id')->toArray())->update(['status' => '2']);

                    // Change Guest status to false: Guest is not in hotel
                    if ($invoice->guests->isNotEmpty()) {
                        Guest::whereIn('id', $invoice->guests->pluck('id')->toArray())->update(['status' => false]);
                    }

                    $status = true;
                }
            } catch (\Throwable $e) {
                Storage::append('invoice.log', $e->getMessage());
            }
        });

        if ($status) {
            flash(trans('common.successful'))->success();

            return back();
        }

        flash(trans('common.error'))->error();

        return back();
    }

    /**
     * Open a closed invoice.
     * Only a manager could open a closed invoice.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function open($id)
    {
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', false)
            ->where('status', true)
            ->first(Fields::parsed('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        $invoice->open = true;

        if ($invoice->save()) {
            flash(trans('common.successful'))->success();

            return back();
        }

        flash(trans('common.error'))->error();

        return back();
    }

    /**
     * Close Payments in a closed invoice.
     * The open status is by default in true value.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function closePayment($id)
    {
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', false)
            ->where('status', true)
            ->where('payment_status', false)
            ->with([
                'payments' => function ($query)
                {
                    $query->select(Fields::get('payments'));
                }
            ])->first(Fields::get('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        if ((float) $invoice->value > $invoice->payments->sum('value')) {
            flash(trans('payments.incomplete'))->info();

            return back();
        }

        $status = false;

        DB::transaction(function () use (&$status, &$invoice) {
            try {
                $invoice->payment_status = true;

                if ($invoice->save()) {
                    $status = true;
                }
            } catch (\Throwable $e) {
                Storage::append('invoice.log', $e->getMessage());
            }
        });

        if ($status) {
            flash(trans('common.successful'))->success();

            return back();
        }

        flash(trans('common.error'))->error();

        return back();
    }

    /**
     * Open a closed invoice.
     * Only a manager could open a closed invoice.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function registerAsLoss($id)
    {
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('status', true)
            ->where('payment_status', false)
            ->where('losses', false)
            ->with([
                'rooms' => function ($query) {
                    $query->select(Fields::get('rooms'));
                },
                'guests' => function ($query) {
                    $query->select(Fields::get('guests'));
                },
                'payments' => function ($query)
                {
                    $query->select(Fields::get('payments'));
                }
            ])->first(Fields::parsed('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        if ($invoice->payment_method) {
            flash(trans('payments.complete'))->info();

            return back();
        }

        if ((float) $invoice->value == $invoice->payments->sum('value')) {
            flash(trans('payments.complete'))->info();

            return back();
        }

        $status = false;

        DB::transaction(function () use (&$status, &$invoice) {
            try {
                $invoice->open = false;
                $invoice->losses = true;

                if ($invoice->save()) {
                    // Change Room status to cleaning
                    Room::whereIn('id', $invoice->rooms->pluck('id')->toArray())->update(['status' => '2']);

                    // Change Guest status to false: Guest is not in hotel
                    if ($invoice->guests->isNotEmpty()) {
                        Guest::whereIn('id', $invoice->guests->pluck('id')->toArray())->update(['status' => false]);
                    }

                    $status = true;
                }
            } catch (\Throwable $e) {
                Storage::append('invoice.log', $e->getMessage());
            }
        });

        if ($status) {
            flash(trans('common.successful'))->success();

            return back();
        }

        flash(trans('common.error'))->error();

        return back();
    }

    /**
     * Show form to change an invoice reservation to check-in reservation.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function takeReservationCheckin($id)
    {
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->where('reservation', true)
            ->with([
                'rooms' => function ($query) {
                    $query->select('id');
                },
                'guests' => function ($query) {
                    $query->select(Fields::get('guests'))
                        ->withPivot('main');
                },
                'company' => function ($query) {
                    $query->select(Fields::get('companies'));
                },
                'payments' => function ($query)
                {
                    $query->select(Fields::get('payments'));
                }
            ])->first(Fields::parsed('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        $customer = Customer::get($invoice);

        return view('app.invoices.reservation-checkin', compact('invoice', 'customer'));
    }

    /**
     * Change an invoice reservation to check-in reservation, includes origin and destination route.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function storeReservationCheckin(StoreRoute $request, $id)
    {
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->where('reservation', true)
            ->first(Fields::parsed('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        $invoice->origin = $request->get('origin', null);
        $invoice->destination = $request->get('destination', null);
        $invoice->reservation = false;

        if ($invoice->save()) {
            flash(trans('common.successful'))->success();

            return redirect()->route('invoices.guests.search', [
                'id' => Hashids::encode($invoice->id)
            ]);
        }

        flash(trans('common.error'))->error();

        return redirect()->route('invoices.show', [
            'id' => Hashids::encode($invoice->id)
        ]);
    }

    public function export()
    {
        $view = view('app.invoices.exports.template')->render();
        // return $view;
        $pdf = App::make('snappy.pdf.wrapper');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('images', true);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('margin-top', 3);
        $pdf->setOption('margin-bottom', 3);
        $pdf->setOption('margin-left', 1);
        $pdf->setOption('margin-right', 1);
        $pdf->loadHTML($view);
        // $pdf = App::make('dompdf.wrapper');
        // $pdf->loadHTML($view);
        return $pdf->download('invoice.pdf');

        // return $pdf->inline();
    }
}
