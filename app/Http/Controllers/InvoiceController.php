<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use App\Helpers\{Age, Fields, Id, Input, Random};
use App\Welkome\{Company, Guest, IdentificationType, Invoice, Product, Room, Service};
use App\Http\Requests\{AddGuests, AddProducts, AddRooms, AddServices, RemoveGuests, StoreInvoiceGuest};

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
            ->get(Fields::get('invoices'))
            ->sortByDesc('created_at');

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
        $invoice->for_job = Input::bool($request->get('for_job'));
        $invoice->user()->associate(auth()->user()->parent);

        if ($invoice->save()) {
            if ($invoice->for_companny) {
                $route = route('invoices.companies.search', [
                    'id' => Hashids::encode($invoice->id)
                ]);
            } else {
                $route = route('invoices.rooms', [
                    'id' => Hashids::encode($invoice->id)
                ]);
            }

            return redirect($route);
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
                    $query->select(Fields::get('companies'));
                },
                'rooms' => function ($query) {
                    $query->select(Fields::parsed('rooms'))
                        ->withPivot('quantity', 'value');
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

        $customer = null;
        if (!$invoice->guests->isEmpty()) {
            $customer = $invoice->guests->first(function ($guest, $index) {
                return $guest->pivot->main == true;
            });
        }

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

        $status = false;

        \DB::transaction(function () use (&$status, $invoice) {
            try {
                if ($invoice->delete()) {
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
            ->where('status', '1') // It is free
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

        if ($invoice->rooms->count() == 0) {
            flash(trans('invoices.firstStep'))->info();

            return redirect()->route('invoices.rooms.add', [
                'id' => Hashids::encode($invoice->id)
            ]);
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
        $guest->status = true; // In hotel
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
    public function addGuests(AddGuests $request, $id)
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

        $guest->dni = $guest->dni;
        $guest->name = $guest->name;
        $guest->last_name = $guest->last_name;
        $guest->email = $guest->email;
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

        $guest->dni = $guest->dni;
        $guest->name = $guest->name;
        $guest->last_name = $guest->last_name;
        $guest->email = $guest->email;
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
        $invoice = Invoice::where('user_id', auth()->user()->parent)
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->with([
                'rooms' => function ($query) {
                    $query->select('id', 'number');
                },
            ])->first(Fields::get('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        $products = Product::where('user_id', auth()->user()->parent)
            ->where('quantity', '>', 0)
            ->where('status', true)
            ->get(Fields::get('products'));

        return view('app.invoices.add-products', compact('invoice', 'products'));
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
                $invoice = Invoice::where('user_id', auth()->user()->parent)
                    ->where('id', Id::get($id))
                    ->where('open', true)
                    ->where('status', true)
                    ->first(Fields::parsed('invoices'));

                $product = Product::where('user_id', auth()->user()->parent)
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
        $invoice = Invoice::where('user_id', auth()->user()->parent)
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->first(Fields::get('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        $services = Service::where('user_id', auth()->user()->parent)
            ->get(Fields::get('services'));

        return view('app.invoices.add-services', compact('invoice', 'services'));
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
                $invoice = Invoice::where('user_id', auth()->user()->parent)
                    ->where('id', Id::get($id))
                    ->where('open', true)
                    ->where('status', true)
                    ->first(Fields::parsed('invoices'));

                $service = Service::where('user_id', auth()->user()->parent)
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
        $invoice = Invoice::where('user_id', auth()->user()->parent)
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->with([
                'company' => function ($query) {
                    $query->select(Fields::get('companies'));
                },
            ])->first(Fields::parsed('invoices'));

        if (empty($invoice)) {
            abort(404);
        }

        if (!empty($invoice->company)) {
            return redirect()->route('invoices.show', [
                'id' => Hashids::encode($invoice->id)
            ]);
        }

        return view('app.invoices.search-companies', compact('invoice'));
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
        $invoice = Invoice::where('user_id', auth()->user()->parent)
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->where('company_id', null)
            ->first(['id']);

        $company = Company::where('user_id', auth()->user()->parent)
            ->where('id', Id::get($company))
            ->first(Fields::get('companies'));

        if (empty($invoice) or empty($company)) {
            abort(404);
        }

        $invoice->company_id = $company->id;

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
     * Show the form for creating a new invoice company.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function createCompanies($id)
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
     * Store a newly created company in storage and attaching to invoice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function storeCompanies(StoreInvoiceGuest $request, $id)
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
        $guest->status = true; // In hotel
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
}
