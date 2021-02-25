<?php

namespace App\Http\Controllers;

use App\Exports\GuestsReport;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Helpers\{Chart, Customer};
use App\Http\Requests\StoreGuest;
use App\Http\Requests\StoreVoucherGuest;
use App\Http\Requests\UpdateGuest;
use App\Models\{Country, Guest, IdentificationType, Room, Voucher};
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
        $guests = Guest::where('user_id', id_parent())
            ->orderBy('created_at', 'DESC')
            ->limit('200')
            ->paginate(config('settings.paginate'), fields_get('guests'));

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
        $guest->identificationType()->associate(id_decode($request->type));
        $guest->user()->associate(id_parent());
        $guest->country()->associate(id_decode($request->nationality));

        if ($guest->save()) {
            flash(trans('common.createdSuccessfully'))->success();

            return redirect()->route('guests.show', [
                'id' => id_encode($guest->id)
            ]);
        }

        flash(trans('common.error'))->error();

        return back();
    }

    /**
     * Show the form for creating a new voucher guest.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function createForVoucher($id)
    {
        $id = id_decode($id);
        $voucher = Voucher::where('user_id', id_parent())
            ->where('id', $id)
            ->where('open', true)
            ->where('status', true)
            ->with([
                'rooms' => function ($query) {
                    $query->select('id', 'number', 'status')
                        ->withPivot('enabled');
                },
                'rooms.guests' => function ($query) use ($id) {
                    $query->select('id', 'name', 'last_name')
                        ->wherePivot('voucher_id', $id);
                },
                'guests' => function ($query) {
                    $query->select(fields_dotted('guests'))
                        ->withPivot('main', 'active');
                },
                'company' => function ($query) {
                    $query->select(fields_get('companies'));
                },
                'payments' => function ($query)
                {
                    $query->select(fields_get('payments'));
                }
            ])->first(fields_get('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        $types = IdentificationType::all(['id', 'type']);
        $countries = Country::all(['id', 'name']);
        $guests = 0;

        $voucher->rooms->each(function ($room) use (&$guests)
        {
            $guests += $room->guests->count();
        });

        $customer = Customer::get($voucher);

        return view('app.guests.create-for-voucher', compact('voucher', 'types', 'guests', 'countries', 'customer'));
    }

    /**
     * Store a newly created guest in storage and attaching to voucher.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function storeForVoucher(StoreVoucherGuest $request, $id)
    {
        $voucher = Voucher::where('user_id', id_parent())
            ->where('id', id_decode($id))
            ->where('open', true)
            ->where('status', true)
            ->with([
                'guests' => function ($query) {
                    $query->select('id');
                },
                'hotel' => function ($query) {
                    $query->select(fields_get('hotels'));
                },
            ])->first(fields_dotted('vouchers'));

        if (empty($voucher)) {
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
        $guest->identificationType()->associate(id_decode($request->type));
        $guest->user()->associate(id_parent());
        $guest->country()->associate(id_decode($request->nationality));

        $isMinor = Customer::isMinor($request->get('birthdate', null));
        $responsible = $request->get('responsible_adult', null);

        if ($isMinor and !empty($responsible)) {
            $guest->responsible_adult = id_decode($responsible);
        }

        if ($guest->save()) {
            $main = $voucher->guests->isEmpty() ? true : false;
            $voucher->guests()->attach($guest->id, [
                'main' => $main,
                'active' => true
            ]);

            $guest->rooms()->attach(id_decode($request->room), [
                'voucher_id' => $voucher->id
            ]);

            // Create Note
            notary($voucher->hotel)->checkinGuest(
                $voucher,
                $guest,
                Room::find(id_decode($request->room))
            );

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
        $guest = Guest::where('user_id', id_parent())
            ->where('id', id_decode($id))
            ->first(fields_get('guests'));

        if (empty($guest)) {
            abort(404);
        }

        $guest->load([
            'vouchers' => function ($query)
            {
                $query->select(fields_dotted('vouchers'))
                    ->whereYear('vouchers.created_at', date('Y'))
                    ->orderBy('vouchers.created_at', 'DESC');
            },
            'vouchers.hotel' => function ($query)
            {
                $query->select('id', 'business_name');
            },
            'country' => function ($query)
            {
                $query->select('id', 'name');
            },
        ]);

        $data = Chart::create($guest->vouchers)
            ->countVouchers()
            ->get();

        return view('app.guests.show', compact('guest', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $guest = Guest::where('user_id', id_parent())
            ->where('id', id_decode($id))
            ->first(fields_get('guests'));

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
        $guest = Guest::where('user_id', id_parent())
            ->where('id', id_decode($id))
            ->first(fields_get('guests'));

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
            $guest->identificationType()->associate(id_decode($request->type));
        }

        if (!empty($request->nationality)) {
            $guest->country()->associate(id_decode($request->nationality));
        }

        if ($guest->save()) {
            flash(trans('common.updatedSuccessfully'))->success();

            return redirect()->route('guests.show', [
                'id' => id_encode($guest->id)
            ]);
        }

        flash(trans('common.error'))->error();

        return redirect()->route('guests.show', [
            'id' => id_encode($guest->id)
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
        $guest = Guest::where('user_id', id_parent())
            ->where('id', id_decode($id))
            ->whereDoesntHave('vouchers')
            ->first(fields_get('guests'));

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
        $query = clean_param($request->get('query', null));

        if (empty($query)) {
            return back();
        }

        $guests = Guest::where('user_id', id_parent())
            ->queryBy($query)
            ->get(fields_get('guests'));

        return view('app.guests.search', compact('guests', 'query'));
    }

    /**
     * Export a listing of guests in excel format.
     *
     * @return \Maatwebsite\Excel\Excel
     */
    public function export()
    {
        $guests = Guest::where('user_id', id_parent())
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
            ->get(fields_get('guests'));

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
    public function toggle($id, $voucher)
    {
        $voucher = Voucher::where('user_id', id_parent())
            ->where('id', id_decode($voucher))
            ->where('open', true)
            ->where('status', true)
            ->with([
                'guests' => function ($query) {
                    $query->select(fields_dotted('guests'))
                        ->where('responsible_adult', false)
                        ->withPivot('main', 'active');
                },
                'guests.rooms' => function ($query) use ($voucher) {
                    $query->select(fields_dotted('rooms'))
                        ->wherePivot('voucher_id', $voucher);
                },
                'guests.identificationType' => function ($query) {
                    $query->select('id', 'type');
                },
                'rooms' => function ($query) {
                    $query->select(fields_dotted('rooms'))
                        ->withPivot('enabled');
                },
                'hotel' => function ($query) {
                    $query->select(fields_get('hotels'));
                }
            ])->firstOrFail(fields_dotted('vouchers'));

        // Check if the voucher only has a guest
        if ($voucher->guests->count() == 1) {
            flash(trans('vouchers.onlyOne'))->error();

            return redirect()->route('vouchers.show', [
                'id' => $voucher->hash,
            ]);
        }

        // The guest
        $guest = $voucher->guests->where('id', id_decode($id))->first();

        // The guest room
        $room = $voucher->rooms->where('id', $guest->rooms()->first()->id)->first();

        // Check if the room is available in the voucher
        if ($room->pivot->enabled) {
            // Toggle status
            // The guest leaves the hotel but remains on the voucher
            if ($guest->status == true and $guest->pivot->active == true) {
                $guest->status = false;

                $voucher->guests()->updateExistingPivot(
                    $guest,
                    [
                        'active' => false
                    ]
                );


            }

            // The guest enters the hotel at the same voucher
            if ($guest->status == false and $guest->pivot->active == false) {
                $guest->status = true;

                $voucher->guests()->updateExistingPivot(
                    $guest,
                    [
                        'active' => true
                    ]
                );
            }

            if ($guest->save()) {
                if ($guest->status) {
                    notary($voucher->hotel)->checkinGuest($voucher, $guest, $room);
                } else {
                    notary($voucher->hotel)->checkoutGuest($voucher, $guest, $room);
                }

                // Check if is the main guest
                if ($guest->pivot->main) {
                    // Update main guest in the voucher
                    $voucher->guests()->updateExistingPivot(
                        $guest,
                        ['main' => false]
                    );

                    // The new main guest
                    $main = $voucher->guests->where('id', '!=', id_decode($id))
                        ->where('pivot.active', true)
                        ->where('status', true)
                        ->first();

                    // Select the main guest
                    $voucher->guests()->updateExistingPivot(
                        $main,
                        ['main' => true]
                    );
                }

                flash(trans('common.updatedSuccessfully'))->success();

                return redirect()->route('vouchers.show', [
                    'id' => $voucher->hash,
                ]);
            }
        }

        flash(trans('common.error'))->error();

        return redirect()->route('vouchers.show', [
            'id' => $voucher->hash,
        ]);
    }
}
