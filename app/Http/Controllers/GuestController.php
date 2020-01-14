<?php

namespace App\Http\Controllers;

use App\Exports\GuestsReport;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Vinkla\Hashids\Facades\Hashids;
use App\Helpers\{Customer, Id, Input, Fields};
use App\Http\Requests\StoreGuest;
use App\Http\Requests\StoreInvoiceGuest;
use App\Http\Requests\UpdateGuest;
use App\User;
use App\Welkome\{Country, Guest, IdentificationType, Invoice};
use Maatwebsite\Excel\Facades\Excel;

class GuestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // TODO: Limitar la consulta en index
        $guests = Guest::where('user_id', Id::parent())
            ->where('status', true) // Guests in hotel
            ->get(Fields::get('guests'));

        $latest = Guest::where('user_id', Id::parent())
            ->where('status', false) // Guests in hotel
            ->orderBy('created_at', 'DESC')
            ->limit(60)
            ->get(Fields::get('guests'));

        $guests = $guests->merge($latest);

        return view('app.guests.index', compact('guests'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $types = IdentificationType::all(['id', 'type']);
        $countries = Country::all(['id', 'name']);

        return view('app.guests.create', compact('types', 'countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreGuest $request)
    {
        $guest = new Guest();
        $guest->name = $request->name;
        $guest->last_name = $request->last_name;
        $guest->dni = $request->dni;
        $guest->email = $request->get('email', null);
        $guest->address = $request->get('address', null);
        $guest->phone = $request->get('phone', null);
        $guest->gender = $request->get('gender', null);
        $guest->birthdate = $request->get('birthdate', null);
        $guest->profession = $request->get('profession', null);
        $guest->status = false; # Not in hotel
        $guest->identificationType()->associate(Id::get($request->type));
        $guest->user()->associate(Id::parent());
        $guest->country()->associate(Id::get($request->nationality));

        if ($guest->save()) {
            flash(trans('common.createdSuccessfully'))->success();

            return redirect()->route('guests.show', [
                'id' => Hashids::encode($guest->id)
            ]);
        }

        flash(trans('common.error'))->error();

        return back();
    }

    /**
     * Show the form for creating a new invoice guest.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function createForInvoice($id)
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
                    $query->select(Fields::parsed('guests'))
                        ->withPivot('main', 'active');
                },
                'company' => function ($query) {
                    $query->select(Fields::get('companies'));
                },
                'payments' => function ($query)
                {
                    $query->select(Fields::get('payments'));
                }
            ])->first(['id', 'number']);

        if (empty($invoice)) {
            abort(404);
        }

        $types = IdentificationType::all(['id', 'type']);
        $countries = Country::all(['id', 'name']);
        $guests = 0;

        $invoice->rooms->each(function ($room) use (&$guests)
        {
            $guests += $room->guests()->count();
        });

        $customer = Customer::get($invoice);

        return view('app.guests.create-for-invoice', compact('invoice', 'types', 'guests', 'countries', 'customer'));
    }

    /**
     * Store a newly created guest in storage and attaching to invoice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function storeForInvoice(StoreInvoiceGuest $request, $id)
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
        $guest->address = $request->get('address', null);
        $guest->phone = $request->get('phone', null);
        $guest->gender = $request->get('gender', null);
        $guest->birthdate = $request->get('birthdate', null);
        $guest->profession = $request->get('profession', null);
        $guest->name = $request->get('name', null);
        $guest->status = true; // In hotel
        $guest->identificationType()->associate(Id::get($request->type));
        $guest->user()->associate(Id::parent());
        $guest->country()->associate(Id::get($request->nationality));

        $isMinor = Customer::isMinor($request->get('birthdate', null));
        $responsible = Id::get($request->get('responsible_adult', null));

        if ($isMinor and !empty($responsible)) {
            $guest->responsible_adult = $responsible;
        }

        if ($guest->save()) {
            $main = $invoice->guests->isEmpty() ? true : false;
            $invoice->guests()->attach($guest->id, [
                'main' => $main,
                'active' => true
            ]);

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
     * Display the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        flash('Característica en proceso de construcción')->info();

        return redirect()->route('guests.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $guest = Guest::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->first(Fields::get('guests'));

        if (empty($guest)) {
            abort(404);
        }

        $guest->load([
            'identificationType' => function ($query)
            {
                $query->select(['id', 'type']);
            },
            'country' => function ($query)
            {
                $query->select(['id', 'name']);
            }
        ]);

        $types = IdentificationType::where('id', '!=', $guest->identificationType->id)
            ->get(['id', 'type']);

        $countries = Country::where('id', '!=', $guest->country->id)
            ->get(['id', 'name']);

        return view('app.guests.edit', compact('guest', 'types', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateGuest $request, $id)
    {
        $guest = Guest::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->first(Fields::get('guests'));

        if (empty($guest)) {
            abort(404);
        }

        $guest->name = $request->name;
        $guest->last_name = $request->last_name;
        $guest->dni = $request->dni;
        $guest->email = $request->get('email', null);
        $guest->address = $request->get('address', null);
        $guest->phone = $request->get('phone', null);
        $guest->gender = $request->get('gender', null);
        $guest->birthdate = $request->get('birthdate', null);
        $guest->profession = $request->get('profession', null);

        if (!empty($request->type)) {
            $guest->identificationType()->associate(Id::get($request->type));
        }

        if (!empty($request->nationality)) {
            $guest->country()->associate(Id::get($request->nationality));
        }

        if ($guest->save()) {
            flash(trans('common.updatedSuccessfully'))->success();

            return redirect()->route('guests.show', [
                'id' => Hashids::encode($guest->id)
            ]);
        }

        flash(trans('common.error'))->error();

        return redirect()->route('guests.show', [
            'id' => Hashids::encode($guest->id)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $guest = Guest::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->whereDoesntHave('invoices')
            ->first(Fields::get('guests'));

        if (empty($guest)) {
            flash(trans('common.notRemovable'))->info();

            return back();
        }

        if ($guest->delete()) {
            flash(trans('common.deletedSuccessfully'))->success();

            return redirect()->route('guests.index');
        }

        flash(trans('common.error'))->error();

        return redirect()->route('guests.index');
    }

    /**
     * Display a listing of searched records.
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $query = Input::clean($request->get('query', null));

        if (empty($query)) {
            return back();
        }

        $guests = Guest::where('user_id', Id::parent())
            ->whereLike(['name', 'last_name', 'dni', 'email'], $query)
            ->get(Fields::get('guests'));

        return view('app.guests.search', compact('guests', 'query'));
    }

    /**
     * Display a JSON list with searched records.
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function searchUnregistered(Request $request)
    {
        if ($request->ajax()) {
            $query = Input::clean($request->get('query', null));

            $guests = Guest::where('user_id', Id::parent())
                ->where('status', false)
                ->whereLike(['name', 'last_name', 'dni', 'email'], $query)
                ->get(Fields::get('guests'));

            return response()->json([
                'guests' => $this->renderToTemplate(
                    $guests,
                    'app.invoices.guests.search',
                    $request->invoice
                    )
            ]);
        }

        abort(403);
    }

    /**
     * Render data collection in a view.
     *
     * @param Illuminate\Support\Collection  $results
     * @return array
     */
    private function renderToTemplate(Collection $results, $template, $invoice)
    {
        $rendered = collect();

        $results->each(function ($guest, $index) use (&$rendered, $template, $invoice) {
            $render = view($template, [
                'guest' => $guest,
                'invoice' => $invoice
            ])->render();

            $rendered->push($render);
        });

        return $rendered->toArray();
    }

    /**
     * Export a listing of guests in excel format.
     *
     * @return \Maatwebsite\Excel\Excel
     */
    public function export()
    {
        $guests = Guest::where('user_id', Id::parent())
            ->with([
                'identificationType' => function ($query)
                {
                    $query->select(['id', 'type']);
                },
                'country' => function ($query)
                {
                    $query->select(['id', 'name']);
                }
            ])
            ->get(Fields::get('guests'));

        if ($guests->isEmpty()) {
            flash(trans('common.noRecords'))->info();

            return redirect()->route('guests.index');
        }

        return Excel::download(new GuestsReport($guests), trans('guests.title') . '.xlsx');
    }

    /**
     * Toggle status for the specified resource from storage.
     *
     * @param  string   $id
     * @return \Illuminate\Http\Response
     */
    public function toggle($id, $invoice)
    {
        $invoice = Invoice::where('user_id', Id::parent())
            ->where('id', Id::get($invoice))
            ->where('open', true)
            ->where('status', true)
            ->with([
                'guests' => function ($query) {
                    $query->select(Fields::parsed('guests'))
                        ->where('responsible_adult', false)
                        ->withPivot('main', 'status');
                },
                'guests.rooms' => function ($query) use ($invoice) {
                    $query->select(Fields::parsed('rooms'))
                        ->wherePivot('invoice_id', $invoice);
                },
                'rooms' => function ($query) {
                    $query->select(Fields::parsed('rooms'))
                        ->withPivot('status');
                },
            ])->first(['id']);

        if (empty($invoice)) {
            return abort(404);
        }

        // Check if the invoice only has a guest
        if ($invoice->guests->count() == 1) {
            flash(trans('invoices.onlyOne'))->error();

            return back();
        }

        // The guest
        $guest = $invoice->guests->where('id', Id::get($id))->first();

        // The guest room
        $room = $invoice->rooms->where('id', $guest->rooms()->first()->id)->first();

        // Check if the room is available in the invoice
        if ($room->pivot->active) {
            // Toggle status
            // The guest leaves the hotel but remains on the invoice
            if ($guest->status == true and $guest->pivot->active == true) {
                $guest->status = false;

                $invoice->guests()->updateExistingPivot(
                    $guest,
                    [
                        'status' => false
                    ]
                );
            }

            // The guest enters the hotel at the same invoice
            if ($guest->status == false and $guest->pivot->active == false) {
                $guest->status = true;

                $invoice->guests()->updateExistingPivot(
                    $guest,
                    [
                        'status' => true
                    ]
                );
            }

            if ($guest->save()) {
                // Check if is the main guest
                if ($guest->pivot->main) {
                    // Update main guest in the invoice
                    $invoice->guests()->updateExistingPivot(
                        $guest,
                        ['main' => false]
                    );

                    // The new main guest
                    $main = $invoice->guests->where('id', '!=', Id::get($id))->where('status', true)->first();

                    // Select the main guest
                    $invoice->guests()->updateExistingPivot(
                        $main,
                        ['main' => true]
                    );
                }

                flash(trans('common.updatedSuccessfully'))->success();

                return back();
            }
        }

        flash(trans('common.error'))->error();

        return back();
    }
}
