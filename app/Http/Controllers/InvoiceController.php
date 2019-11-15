<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use App\Helpers\{Age, Fields, Id, Input, Random};
use App\Welkome\{Company, Country, Guest, Hotel, IdentificationType, Invoice, Product, Room, Service};
use App\Http\Requests\{
    AddGuests,
    AddProducts,
    AddRooms,
    AddServices,
    Multiple,
    RemoveGuests,
    StoreInvoice,
    StoreInvoiceGuest
};

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
            ->where('open', true)
            ->where('status', true)
            ->with([
                'hotel' => function ($query) {
                    $query->select(Fields::get('hotels'));
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

        // \DB::transaction(function () use (&$status, $request, $numbers) {
            // try {
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
                    $value = $quantity * $selected['price'];

                    $attach[$room->id] = [
                        'quantity' => $quantity,
                        'value' => $value,
                        'start' => $start->toDateString(),
                        'end' => $end->toDateString()
                    ];
                }

                foreach ($attach as $key => $item) {
                    $invoice->subvalue += $item['value'];
                    $invoice->value += $item['value'];
                }
                // TODO: Crear procedimiento para incrementar el valor diario para END null
                if ($invoice->save()) {
                    Room::where('user_id', Id::parent())
                        ->whereIn('number', $numbers->pluck('number')->toArray())
                        ->where('hotel_id', Id::get($request->hotel))
                        ->update(['status' => '0']);

                    $invoice->rooms()->sync($attach);

                    $status = true;

                    session()->forget('hotel');
                    session()->forget('rooms');
                }
            // } catch (\Throwable $e) {
                // ..
            // }
        // });

        if ($status) {
            flash(trans('common.successful'))->success();

            return redirect()->route('invoices.guests.search', [
                'id' => Hashids::encode($invoice->id)
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
                'rooms' => function ($query) {
                    $query->select(Fields::parsed('rooms'))
                        ->withPivot('quantity', 'value', 'start', 'end');
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
                        ->withPivot('quantity', 'value');
                },
                'services' => function ($query) {
                    $query->select(Fields::parsed('services'))
                        ->withPivot('quantity', 'value');
                },
            ])->first(Fields::parsed('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        $customer = $this->getCustomer($invoice);

        return view('app.invoices.show', compact('invoice', 'customer'));
    }

    /**
     * Return the invoice customer.
     *
     * @param  \App\Welkome\Invoice
     * @return array
     */
    public function getCustomer(Invoice $invoice): array
    {
        $customer = [];

        if (empty($invoice->company)) {
            if ($invoice->guests->isNotEmpty()) {
                $main = $invoice->guests->first(function ($guest, $index) {
                    return $guest->pivot->main == true;
                });

                $customer['name'] = $main->full_name;
                $customer['tin'] = $main->dni;
                $customer['route'] = route('guests.show', ['id' => Hashids::encode($main->id)]);
            }
        } else {
            $customer['name'] = $invoice->company->business_name;
            $customer['tin'] = $invoice->company->tin;
            $customer['route'] = route('guests.show', ['id' => Hashids::encode($invoice->company->id)]);
        }

        return $customer;
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
                }
            ])->first(Fields::get('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        $status = false;

        \DB::transaction(function () use (&$status, $invoice) {
            try {
                if ($invoice->delete()) {
                    Room::whereIn('id', $invoice->rooms->pluck('id')->toArray())->update(['status' => '1']);

                    $status = true;
                }
            } catch (\Throwable $e) {
                \Storage::append('invoice.log', $e->getMessage());
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
                }
            ])->first(Fields::get('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        $rooms = Room::where('hotel_id', $invoice->hotel_id)
            ->where('status', '1') // It is free
            ->get(Fields::get('rooms'));

        if ($rooms->isEmpty()) {
            flash('No hay habitaciones disponibles')->info();

            return redirect()->route('invoices.show', [
                'id' => Hashids::encode($invoice->id)
            ]);
        }

        $customer = $this->getCustomer($invoice);

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

        // \DB::transaction(function () use (&$status, $request, $id) {
            // try {
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
                $value = $quantity * $room->price;

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
                $invoice->save();

                $room->status = '0';
                $room->save();
                $status = true;
            // } catch (\Throwable $e) {
                // ..
            // }
        // });

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

        $customer = $this->getCustomer($invoice);

        return view('app.invoices.search-guests', compact('invoice', 'customer'));
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
                }
            ])->first(['id']);

        if (empty($invoice)) {
            abort(404);
        }

        $types = IdentificationType::all(['id', 'type']);
        $countries = Country::all(['id', 'name']);
        $guests = $this->countGuestsPerRoom($invoice);

        $customer = $this->getCustomer($invoice);

        return view('app.invoices.guests.create', compact('invoice', 'types', 'guests', 'countries', 'customer'));
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
        $invoice = Invoice::where('user_id', Id::parent())
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
        $guest->profession = $request->get('profession', null);
        $guest->name = $request->get('name', null);
        $guest->status = true; // In hotel
        $guest->identificationType()->associate(Id::get($request->type));
        $guest->user()->associate(Id::parent());
        $guest->country()->associate(Id::get($request->nationality));

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

        $customer = $this->getCustomer($invoice);

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

        $responsible = Id::get($request->get('responsible_adult', null));

        if ($this->isMinor($guest->birthdate) and !empty($responsible)) {
            $guest->responsible_adult = $responsible;
        }

        $main = $invoice->guests->isEmpty() ? true : false;
        $invoice->guests()->attach($guest->id, ['main' => $main]);

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
     * @param  \Illuminate\Http\Request  $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function removeGuests(RemoveGuests $request, $id)
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
            ->where('status', true) // In hotel
            ->first(Fields::get('guests'));

        if (empty($invoice) or empty($guest)) {
            abort(404);
        }

        if ($invoice->guests->count() == 1) {
            flash(trans('invoices.onlyOne'))->error();

            return back();
        }

        $invoice->guests()->detach($guest->id);
        $invoice->load([
            'guests' => function ($query) {
                $query->select('id')
                    ->where('responsible_adult', false);
            }
        ]);

        $invoice->guests()->updateExistingPivot(
            $invoice->guests->first(),
            ['main' => true]
        );

        $guest->rooms()
            ->wherePivot('invoice_id', $invoice->id)
            ->detach(Id::get($request->room));

        $guest->status = false;
        $guest->save();

        flash(trans('common.successful'))->success();

        return redirect()->route('invoices.show', [
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
        // TODO: Pensar en crear una tabla de atributos de los invoice
        // TODO: Crear tabla de configuraciones
        // Agregar edad limite para ser adulto
        // Agregar Hora hotelera
        if ($age < 18) {
            return true;
        }

        return false;
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
                }
            ])->first(Fields::get('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        $products = Product::where('user_id', Id::parent())
            ->where('quantity', '>', 0)
            ->where('status', true)
            ->get(Fields::get('products'));

        if ($products->isEmpty()) {
            flash('No hay productos disponibles')->info();

            return redirect()->route('invoices.show', [
                'id' => Hashids::encode($invoice->id)
            ]);
        }

        $customer = $this->getCustomer($invoice);

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

        \DB::transaction(function () use (&$status, $request, $id) {
            try {
                $invoice = Invoice::where('user_id', Id::parent())
                    ->where('id', Id::get($id))
                    ->where('open', true)
                    ->where('status', true)
                    ->first(Fields::parsed('invoices'));

                $product = Product::where('user_id', Id::parent())
                    ->where('id', Id::get($request->product))
                    ->where('quantity', '>', 0)
                    ->where('status', true)
                    ->first(Fields::get('products'));

                $value = (int) $request->quantity * $product->price;

                $invoice->products()->attach(
                    $product->id,
                    [
                        'quantity' => $request->quantity,
                        'value' => $value,
                    ]
                );

                $invoice->subvalue += $value;
                $invoice->value += $value;
                $invoice->update();

                $product->quantity -= $request->quantity;

                // if (($product->quantity - $request->quantity) == 0) {
                //     $product->status = false;
                // }

                $product->update();

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
            ->with([
                'guests' => function ($query) {
                    $query->select(Fields::get('guests'))
                        ->withPivot('main');
                },
                'company' => function ($query) {
                    $query->select(Fields::get('companies'));
                }
            ])
            ->first(Fields::get('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        $services = Service::where('user_id', Id::parent())
            ->whereStatus(true)
            ->get(Fields::get('services'));

        if ($services->isEmpty()) {
            flash('No hay servicios disponibles')->info();

            return redirect()->route('invoices.show', [
                'id' => Hashids::encode($invoice->id)
            ]);
        }

        $customer = $this->getCustomer($invoice);

        return view('app.invoices.add-services', compact('invoice', 'services', 'customer'));
    }

    /**
     * Store the product values to invoice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function addServices(AddServices $request, $id)
    {
        $status = false;

        \DB::transaction(function () use (&$status, $request, $id) {
            try {
                $invoice = Invoice::where('user_id', Id::parent())
                    ->where('id', Id::get($id))
                    ->where('open', true)
                    ->where('status', true)
                    ->first(Fields::parsed('invoices'));

                $service = Service::where('user_id', Id::parent())
                    ->whereStatus(true)
                    ->first(Fields::get('services'));

                $value = (int) $request->quantity * $service->price;

                $invoice->services()->attach(
                    $service->id,
                    [
                        'quantity' => $request->quantity,
                        'value' => $value,
                    ]
                );

                $invoice->subvalue += $value;
                $invoice->value += $value;
                $invoice->update();

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
                }
            ])
            ->first(Fields::parsed('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        $customer = $this->getCustomer($invoice);

        return view('app.invoices.search-companies', compact('invoice', 'customer'));
    }

    /**
     * Store a newly created company in storage and attaching to invoice.
     *
     * @param $id
     * @param $company
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
     * Create a new invoice with many rooms.
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
}
