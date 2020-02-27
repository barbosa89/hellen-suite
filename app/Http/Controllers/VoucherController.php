<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use App\Helpers\{Customer, Fields, Id, Input, Random};
use App\Welkome\{Additional, Company, Guest, Hotel, Voucher, Product, Room, Service, Shift, Vehicle};
use App\Http\Requests\{
    AddGuests,
    AddProducts,
    AddRooms,
    AddServices,
    ChangeGuestRoom,
    Multiple,
    ChangeRoom,
    VouchersProcessing,
    StoreAdditional,
    StoreVoucher,
    StoreRoute
};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

// TODO: Crear tabla de configuraciones
// Agregar edad limite para ser adulto
// Agregar Hora hotelera
class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = Voucher::query();
        $query->where('user_id', Id::parent())
            ->where('status', true)
            ->where('type', 'lodging')
            ->with([
                'hotel' => function ($query) {
                    $query->select(Fields::get('hotels'));
                },
                'guests' => function ($query) {
                    $query->select(Fields::parsed('guests'))
                        ->wherePivot('main', true)
                        ->withPivot('main', 'active');
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

        $vouchers = $query->get(Fields::parsed('vouchers'));
        $vouchers = $vouchers->sortByDesc('created_at');

        return view('app.vouchers.index', compact('vouchers'));
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
                        ->select(Fields::parsed('rooms'));
                }
            ])->first(Fields::get('hotels'));


        return view('app.vouchers.create', compact('hotel'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreVoucher $request)
    {
        $status = false;
        $numbers = collect($request->room);
        $voucherId = null;

        DB::transaction(function () use (&$status, &$voucherId, $request, $numbers) {
            try {
                $voucher = $this->new();
                $voucher->origin = $request->get('origin', null);
                $voucher->destination = $request->get('destination', null);
                $voucher->hotel()->associate(Id::get($request->hotel));

                if ($request->registry == 'reservation') {
                    $voucher->reservation = true;
                }

                $rooms = Room::where('user_id', Id::parent())
                    ->whereIn('number', $numbers->pluck('number')->toArray())
                    ->where('hotel_id', Id::get($request->hotel))
                    ->where('status', '1')
                    ->get(Fields::parsed('rooms'));

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
                        'price' => $selected['price'],
                        'quantity' => $quantity,
                        'discount' => $discount,
                        'subvalue' => $subvalue,
                        'taxes' => $taxes,
                        'value' => $subvalue + $taxes,
                        'start' => $start->toDateString(),
                        'end' => $end->toDateString(),
                        'enabled' => true
                    ];
                }

                foreach ($attach as $key => $item) {
                    $voucher->discount += $item['discount'];
                    $voucher->subvalue += $item['subvalue'];
                    $voucher->taxes += $item['taxes'];
                    $voucher->value += $item['value'];
                }

                if ($voucher->save()) {
                    Room::where('user_id', Id::parent())
                        ->whereIn('number', $numbers->pluck('number')->toArray())
                        ->where('hotel_id', Id::get($request->hotel))
                        ->update(['status' => '0']);

                    // Get the shift
                    $shift = Shift::current(Id::get($request->hotel));

                    $voucher->shifts()->attach($shift);
                    $voucher->rooms()->sync($attach);
                    $voucherId = $voucher->id;
                    $status = true;

                    session()->forget('hotel');
                    session()->forget('rooms');
                }
            } catch (\Throwable $e) {
                Storage::append('voucher.log', $e->getMessage());
            }
        });

        if ($status) {
            flash(trans('common.successful'))->success();

            return redirect()->route('vouchers.guests.search', [
                'id' => Hashids::encode($voucherId)
            ]);
        }

        flash(trans('common.error'))->error();

        return redirect()->route('vouchers.index');
    }

    /**
     * Return a newly Voucher instance.
     *
     * @return \App\Welkome\Voucher
     */
    private function new()
    {
        $voucher = new Voucher();
        $voucher->number = Random::consecutive();
        $voucher->subvalue = 0.0;
        $voucher->taxes = 0.0;
        $voucher->discount = 0.0;
        $voucher->value = 0.0;
        $voucher->status = true;
        $voucher->user()->associate(Id::parent());

        return $voucher;
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
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', $id)
            ->where('status', true)
            ->first(Fields::parsed('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        $voucher->load([
            'guests' => function ($query) use ($id) {
                $query->select(Fields::parsed('guests'))
                    ->withPivot('main', 'active');
            },
            'guests.vehicles' => function ($query) use ($id) {
                $query->select(Fields::parsed('vehicles'))
                    ->wherePivot('voucher_id', $id);
            },
            'guests.rooms' => function ($query) use ($id) {
                $query->select(Fields::parsed('rooms'))
                    ->wherePivot('voucher_id', $id);
            },
            'guests.parent' => function ($query) {
                $query->select('id', 'name', 'last_name');
            },
            'guests.identificationType' => function ($query) {
                $query->select('id', 'type');
            },
            'company' => function ($query) {
                $query->select(Fields::get('companies'));
            },
            'rooms' => function ($query) {
                $query->select(Fields::parsed('rooms'))
                    ->withPivot('quantity', 'discount', 'subvalue', 'taxes', 'value', 'start', 'end', 'price', 'enabled');
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
                $query->select(['id', 'description', 'billable','value', 'voucher_id', 'created_at']);
            },
            'payments' => function ($query)
            {
                $query->select(Fields::get('payments'));
            },
            'props' => function ($query) {
                $query->select(Fields::parsed('props'))
                    ->withPivot('quantity', 'value', 'created_at');
            },
        ]);

        $customer = Customer::get($voucher);
        $view = $this->getView($voucher);
        // dd($voucher, $view);

        return view($view, compact('voucher', 'customer'));
    }

    public function getView(Voucher $voucher)
    {
        if ($voucher->open) {
            return 'app.vouchers.show';
        }

        return 'app.vouchers.show-static';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->with([
                'rooms' => function ($query) {
                    $query->select(Fields::parsed('rooms'));
                },
                'guests' => function ($query) {
                    $query->select(Fields::parsed('guests'));
                },
                'products' => function ($query) {
                    $query->select(Fields::parsed('products'))
                        ->withPivot('id', 'quantity', 'value');
                },
                'props' => function ($query) {
                    $query->select(Fields::parsed('props'))
                        ->withPivot('quantity');
                }
            ])->first(Fields::parsed('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        $status = false;

        DB::transaction(function () use (&$status, &$voucher) {
            try {
                // Change Room status to cleaning
                Room::whereIn('id', $voucher->rooms->pluck('id')->toArray())->update(['status' => '2']);

                // Change Guest status to false: Guest is not in hotel
                if ($voucher->guests->isNotEmpty()) {
                    Guest::whereIn('id', $voucher->guests->pluck('id')->toArray())
                        ->whereDoesntHave('vouchers', function (Builder $query) use ($voucher) {
                            $query->where('open', true)
                                ->where('status', true)
                                ->where('reservation', false)
                                ->where('created_at', '>', $voucher->created_at);
                        })->update(['status' => false]);
                }

                // Restore product stocks
                if ($voucher->products->isNotEmpty()) {
                    $voucher->products->each(function ($product)
                    {
                        $product->quantity += $product->pivot->quantity;
                        $product->save();
                    });
                }

                // Restore prop stocks
                if ($voucher->props->isNotEmpty()) {
                    $voucher->props->each(function ($prop)
                    {
                        $prop->quantity += $prop->pivot->quantity;
                        $prop->save();
                    });
                }

                $voucher->delete();
                $status = true;
            } catch (\Throwable $e) {
                Storage::append('voucher.log', $e->getMessage());
            }
        });

        if ($status) {
            flash(trans('common.successful'))->success();

            return redirect()->route('vouchers.index');
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

        $vouchers = Voucher::where('user_id', Id::parent())
            ->whereLike([
                'number',
                'guests.name',
                'guests.last_name',
                'guests.dni',
                'company.business_name',
                'hotel.business_name'
            ], $query)->paginate(
                config('welkome.paginate'),
                Fields::parsed('vouchers')
            );

        return view('app.vouchers.search', compact('vouchers', 'query'));
    }

    /**
     * Show the form for adding rooms to voucher.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function showFormToAddRooms($id = '')
    {
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->with([
                'hotel' => function ($query) {
                    $query->select(Fields::get('hotels'));
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
            ])->first(Fields::parsed('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        $rooms = Room::where('hotel_id', $voucher->hotel->id)
            ->where('status', '1') // It is free
            ->get(Fields::parsed('rooms'));

        if ($rooms->isEmpty()) {
            flash('No hay habitaciones disponibles')->info();

            return redirect()->route('vouchers.show', [
                'id' => Hashids::encode($voucher->id)
            ]);
        }

        $customer = Customer::get($voucher);

        return view('app.vouchers.add-rooms', compact('voucher', 'rooms', 'customer'));
    }

    /**
     * Attach the selected rooms to voucher.
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
                $voucher = Voucher::where('user_id', Id::parent())
                    ->where('id', Id::get($id))
                    ->where('open', true)
                    ->where('status', true)
                    ->with([
                        'hotel' => function ($query) {
                            $query->select(Fields::get('hotels'));
                        }
                    ])->first(Fields::parsed('vouchers'));

                $room = Room::where('user_id', Id::parent())
                    ->where('number', $request->number)
                    ->where('hotel_id', $voucher->hotel->id)
                    ->where('status', '1')
                    ->first(Fields::parsed('rooms'));

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

                $voucher->rooms()->attach(
                    $room->id,
                    [
                        'price' => $request->price,
                        'quantity' => $quantity,
                        'discount' => $discount,
                        'subvalue' => $subvalue,
                        'taxes' => $taxes,
                        'value' => $subvalue + $taxes,
                        'start' => $request->start,
                        'end' => $end->toDateString(),
                        'enabled' => true
                    ]
                );

                $voucher->discount += $discount;
                $voucher->subvalue += $subvalue;
                $voucher->taxes += $taxes;
                $voucher->value += $subvalue + $taxes;
                $voucher->save();

                $room->status = '0';
                $room->save();
                $status = true;
            } catch (\Throwable $e) {
                Storage::append('voucher.log', $e->getMessage());
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
     * Redirect to create new voucher with many rooms.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createWithMultipleRooms(Multiple $request)
    {
        $rooms = collect($request->rooms);

        session()->put('hotel', $request->hotel);
        session()->put('rooms', $rooms->implode('hash', ','));

        // TODO: Change to common link in view component, cambiar este método de creación
        return response()->json([
            'redirect' => '/vouchers/create'
        ]);
    }

    /**
     * Show the form to change a room from the voucher.
     *
     * @param int $id
     * @param int $room
     * @return \Illuminate\Http\Response
     */
    public function showFormToChangeRoom($id, $room)
    {
        $id = Id::get($id);
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', $id)
            ->where('open', true)
            ->where('status', true)
            ->first(Fields::parsed('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        $voucher->load([
            'hotel' => function ($query) {
                $query->select(Fields::get('hotels'));
            },
            'guests' => function ($query) {
                $query->select(Fields::parsed('guests'))
                    ->withPivot('main', 'active');
            },
            'company' => function ($query) {
                $query->select(Fields::get('companies'));
            },
            'rooms' => function ($query) {
                $query->select(Fields::parsed('rooms'))
                    ->withPivot('quantity', 'discount', 'subvalue', 'taxes', 'value', 'start', 'end', 'price', 'enabled');
            },
            'rooms.guests' => function ($query) use ($id) {
                $query->select(Fields::parsed('guests'))
                    ->wherePivot('voucher_id', $id);
            },
            'payments' => function ($query)
            {
                $query->select(Fields::get('payments'));
            }
        ]);

        $room = $voucher->rooms->where('id', Id::get($room))
            ->where('hotel_id', $voucher->hotel->id)
            ->first();

        // Check if room is enabled to changes
        if ($room->pivot->enabled == false) {
            flash(trans('vouchers.delivered.room'))->info();

            return back();
        }

        $rooms = Room::where('hotel_id', $voucher->hotel->id)
            ->where('status', '1') // It is free
            ->get(Fields::parsed('rooms'));

        if ($rooms->isEmpty()) {
            flash('No hay habitaciones disponibles')->info();

            return redirect()->route('vouchers.show', [
                'id' => Hashids::encode($voucher->id)
            ]);
        }

        $customer = Customer::get($voucher);

        return view('app.vouchers.change-room', compact('voucher', 'rooms', 'customer', 'room'));
    }

    // TODO: Procedimiento para actualizar quitar responsable a quienes hayan pasado el mínimo de edad
    /**
     * Change a room in the voucher with relationships.
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
                $voucher = Voucher::where('user_id', Id::parent())
                    ->where('id', $id)
                    ->where('open', true)
                    ->where('status', true)
                    ->first(Fields::parsed('vouchers'));

                if (empty($voucher)) {
                    abort(404);
                }

                $voucher->load([
                    'hotel' => function ($query) {
                        $query->select(Fields::get('hotels'));
                    },
                    'guests' => function ($query) {
                        $query->select(Fields::parsed('guests'))
                            ->withPivot('main', 'active');
                    },
                    'company' => function ($query) {
                        $query->select(Fields::get('companies'));
                    },
                    'rooms' => function ($query) {
                        $query->select(Fields::parsed('rooms'))
                            ->withPivot('quantity', 'discount', 'subvalue', 'taxes', 'value', 'start', 'end', 'price', 'enabled');
                    },
                    'rooms.guests' => function ($query) use ($id) {
                        $query->select(Fields::parsed('guests'))
                            ->wherePivot('voucher_id', $id);
                    }
                ]);

                // The room to change from the voucher
                $current = $voucher->rooms->where('id', Id::get($roomId))
                    ->where('hotel_id', $voucher->hotel->id)
                    ->first();

                    // Check if room is enabled to changes
                if ($current->pivot->enabled == false) {
                    // The new room to add to the voucher
                    $room = Room::where('hotel_id', $voucher->hotel->id)
                        ->where('number', $request->number)
                        ->where('status', '1') // It is free
                        ->first(Fields::parsed('rooms'));

                    ### Rooms ###

                    // Current values are subtracted
                    $voucher->discount -= $current->pivot->discount;
                    $voucher->subvalue -= $current->pivot->subvalue;
                    $voucher->taxes -= $current->pivot->taxes;
                    $voucher->value -= $current->pivot->value;

                    // Detach current room
                    $voucher->rooms()->detach($current->id);

                    // Calculating the new values
                    $discount = ($room->price - $request->price) * $current->pivot->quantity;
                    $taxes = ($request->price * $room->tax) * $current->pivot->quantity;
                    $subvalue = $request->price * $current->pivot->quantity;

                    $voucher->rooms()->attach(
                        $room->id,
                        [
                            'price' => $request->price,
                            'quantity' => $current->pivot->quantity, // This is same that before
                            'discount' => $discount,
                            'subvalue' => $subvalue,
                            'taxes' => $taxes,
                            'value' => $subvalue + $taxes,
                            'start' => $current->pivot->start, // This is same that before
                            'end' => $current->pivot->end, // This is same that before
                            'enabled' => true
                        ]
                    );

                    // Summing the new values
                    $voucher->discount += $discount;
                    $voucher->subvalue += $subvalue;
                    $voucher->taxes += $taxes;
                    $voucher->value += $subvalue + $taxes;
                    $voucher->save();

                    ### Guests ###

                    // Check the room has guests
                    if ($current->guests->isNotEmpty()) {
                        foreach ($current->guests as $guest) {
                            // Detach room of the guests
                            $guest->rooms()
                                ->wherePivot('voucher_id', $voucher->id)
                                ->detach($current->id);

                            // Attach the new room of the guests
                            $guest->rooms()->attach($room->id, [
                                'voucher_id' => $voucher->id
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
                }
            } catch (\Throwable $e) {
                Storage::append('voucher.log', $e->getMessage());
            }
        });

        if ($status) {
            flash(trans('common.successful'))->success();
        } else {
            flash(trans('common.error'))->error();
        }

        return redirect()->route('vouchers.show', [
            'id' => $id
        ]);
    }

    /**
     * Deliver a room on the voucher. The guests also leave.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deliverRoom($id, $roomId)
    {
        $id = Id::get($id);
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', $id)
            ->where('open', true)
            ->where('status', true)
            ->where('reservation', false)
            ->where('losses', false)
            ->first(Fields::parsed('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        $voucher->load([
            'rooms' => function ($query) {
                $query->select(Fields::parsed('rooms'))
                    ->withPivot('quantity', 'discount', 'subvalue', 'taxes', 'value', 'start', 'end', 'price', 'enabled');
            },
            'rooms.guests' => function ($query) use ($id) {
                $query->select(Fields::parsed('guests'))
                    ->wherePivot('voucher_id', $id);
            },
            'company' => function ($query) {
                $query->select(Fields::get('companies'));
            },
            'guests' => function ($query) {
                $query->select(Fields::parsed('guests'))
                    ->withPivot('main', 'active');
            }
        ]);

        // Check if the voucher has one room
        if ($voucher->rooms->where('pivot.enabled')->count() == 1) {
            flash(trans('vouchers.has.one.room'))->info();

            return back();
        }

        // Get the room
        $room = $voucher->rooms->where('id', Id::get($roomId))->first();

        // Check if the room is active in the voucher. Only prevention.
        if ($room->pivot->enabled == false) {
            flash(trans('vouchers.delivered.room'))->info();

            return back();
        }

        // If the voucher hasn't a company as customer, then check if this room has the main guest
        if (empty($voucher->company)) {
            if ($room->guests->count() > 0) {
                foreach ($room->guests as $guest) {
                    if ($voucher->guests->where('id', $guest->id)->first()->pivot->main) {
                        flash(trans('vouchers.main.guest'))->info();

                        return back();
                    }
                }
            }
        }

        $room->status = '2';

        if ($room->save()) {
            // Change room status in the voucher relationship
            $voucher->rooms()->updateExistingPivot(
                $room,
                ['enabled' => false]
            );

            // Each guest in the room must leave
            if ($room->guests->count() > 0) {
                foreach ($room->guests as $guest) {
                    $guest->status = false;

                    if ($guest->save()) {
                        $voucher->guests()->updateExistingPivot(
                            $guest,
                            [
                                'active' => false
                            ]
                        );
                    }
                }
            }

            flash(trans('common.successful'))->info();

            return back();
        }

        flash(trans('common.error'))->error();

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
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->with([
                'rooms' => function ($query) {
                    $query->select('id');
                },
                'rooms.guests' => function ($query) use ($id) {
                    $query->select('id', 'name', 'last_name')
                        ->wherePivot('voucher_id', Id::get($id));
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
            ])->first(Fields::parsed('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        if ($voucher->rooms->count() == 0) {
            flash(trans('vouchers.firstStep'))->info();

            return redirect()->route('vouchers.rooms.add', [
                'id' => Hashids::encode($voucher->id)
            ]);
        }

        $customer = Customer::get($voucher);

        return view('app.vouchers.search-guests', compact('voucher', 'customer'));
    }

    /**
     * Show form to add guests to voucher.
     *
     * @param $id
     * @param $guest
     * @return \Illuminate\Http\Response
     */
    public function showFormToAddGuests($id, $guest)
    {
        $id = Id::get($id);
        $voucher = Voucher::where('user_id', Id::parent())
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
            ])->first(Fields::parsed('vouchers'));

        $guest = Guest::where('id', Id::get($guest))
            ->where('status', false) // Not in hotel
            ->with([
                'identificationType' => function ($query) {
                    $query->select('id', 'type');
                },
            ])->first(Fields::parsed('guests'));

        if (empty($voucher) or empty($guest)) {
            abort(404);
        }

        $guests = $this->countGuestsPerRoom($voucher);

        $customer = Customer::get($voucher);

        return view('app.vouchers.add-guests', compact('voucher', 'guest', 'guests', 'customer'));
    }

    /**
     * Return number of guests in all rooms.
     *
     * @param  \App\Welkome\Voucher  $voucher
     * @return int
     */
    private function countGuestsPerRoom(Voucher $voucher): int
    {
        $guests = 0;

        $voucher->rooms->each(function ($room) use (&$guests)
        {
            $guests += $room->guests()->count();
        });

        return $guests;
    }

    /**
     * Add guests to voucher.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function addGuests(AddGuests $request, $id)
    {
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->with([
                'guests' => function ($query) {
                    $query->select('id');
                },
                'rooms' => function ($query) {
                    $query->select('id', 'number', 'status')
                        ->withPivot('enabled');
                }
            ])->first(['id']);

        $guest = $voucher->guests->where('id', Id::get($request->guest))->first();

        if (empty($guest)) {
            // The guest to add to the voucher
            $guest = Guest::where('id', Id::get($request->guest))
                ->where('status', false) // Not in hotel
                ->first(Fields::parsed('guests'));
        }

        if (empty($voucher) or empty($guest)) {
            abort(404);
        }

        // Selected room
        $room = $voucher->rooms->where('id', Id::get($request->room))->first();

        // Check if selected room is disabled in the current voucher
        if ($room->pivot->enabled == false) {
            flash(trans('vouchers.delivered.room'))->info();

            return redirect()->route('vouchers.show', [
                'id' => Hashids::encode($voucher->id)
            ]);
        }

        // Check if the guest to add exists in the current guest
        if ($voucher->guests->where('id', $guest->id)->count() == 0) {
            $responsible = Id::get($request->get('responsible_adult', null));

            // Assign a responsible adult
            if (Customer::isMinor($guest->birthdate) and !empty($responsible)) {
                $guest->responsible_adult = $responsible;
            }

            $voucher->guests()->attach($guest->id, [
                'main' => $voucher->guests->isEmpty(), // Check if the guest is the first so to assign the main guest
                'active' => true
            ]);
        } else {
            // Refresh curren relationship voucher - guest
            $voucher->guests()->updateExistingPivot(
                $guest,
                ['active' => true]
            );
        }

        // Remove old relationships guest - room
        $guest->rooms()
            ->wherePivot('voucher_id', $voucher->id)
            ->detach();

        // Refresh relationships guest - room
        $guest->rooms()->attach($room, [
            'voucher_id' => $voucher->id
        ]);

        // Change guest status
        $guest->status = true;
        $guest->save();

        flash(trans('common.successful'))->success();

        return redirect()->route('vouchers.guests.search', [
            'id' => Hashids::encode($voucher->id)
        ]);
    }

    /**
     * Remove guests to voucher.
     *
     * @param int $id
     * @param int $guestId
     * @return \Illuminate\Http\Response
     */
    public function removeGuests($id, $guestId)
    {
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->with([
                'guests' => function ($query) {
                    $query->select('id')
                        ->where('responsible_adult', false)
                        ->withPivot('active');
                },
                'guests.rooms' => function ($query) use ($id) {
                    $query->select('id', 'number')
                        ->wherePivot('voucher_id', Id::get($id));
                },
                'rooms' => function ($query) {
                    $query->select(Fields::parsed('rooms'))
                        ->withPivot('enabled');
                },
            ])->first(['id']);

        // Check if the voucher only has a guest
        if ($voucher->guests->count() == 1 or $voucher->guests->where('pivot.active', true)->count() == 1) {
            flash(trans('vouchers.onlyOne'))->error();

            return back();
        }

        // Get the guest to remove from voucher
        $guest = $voucher->guests->where('id', Id::get($guestId))->first();

        // Check if the guest room isn't enabled to changes
        if ($voucher->rooms->where('id', $guest->rooms->first()->id)->first()->pivot->enabled == false) {
            flash(trans('vouchers.delivered.room'))->info();

            return back();
        }

        // Check if the guest is inactive in the current voucher
        if ($guest->pivot->active == false) {
            flash(trans('vouchers.inactive.guest'))->error();

            return back();
        }

        // Check if voucher or guest are null
        if (empty($voucher) or empty($guest)) {
            abort(404);
        }

        // Detach the guest from voucher
        $voucher->guests()->detach($guest->id);

        // Refresh the guests relationship to select the main guest
        $voucher->load([
            'guests' => function ($query) {
                $query->select('id')
                    ->where('responsible_adult', false);
            }
        ]);

        // Select the main guest
        $voucher->guests()->updateExistingPivot(
            $voucher->guests->first(),
            ['main' => true]
        );

        // Detach the guest from room
        $guest->rooms()
            ->wherePivot('voucher_id', $voucher->id)
            ->detach($guest->rooms->first()->id);

        // The guest will be available to add to voucher
        $guest->status = false;
        $guest->save();

        flash(trans('common.successful'))->success();

        return redirect()->route('vouchers.show', [
            'id' => Hashids::encode($voucher->id)
        ]);
    }

    /**
     * Show the form to change guest room from the voucher.
     *
     * @param int $id
     * @param int $guest
     * @return \Illuminate\Http\Response
     */
    public function showFormToChangeGuestRoom($id, $guest)
    {
        $id = Id::get($id);
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', $id)
            ->where('open', true)
            ->where('status', true)
            ->first(Fields::parsed('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        $voucher->load([
            'hotel' => function ($query) {
                $query->select(Fields::get('hotels'));
            },
            'guests' => function ($query) {
                $query->select(Fields::parsed('guests'))
                    ->withPivot('main', 'active');
            },
            'guests.rooms' => function ($query) use ($id) {
                $query->select(Fields::parsed('rooms'))
                    ->wherePivot('voucher_id', $id);
            },
            'company' => function ($query) {
                $query->select(Fields::get('companies'));
            },
            'rooms' => function ($query) use ($id) {
                $query->select(Fields::parsed('rooms'))
                    ->wherePivot('enabled', true)
                    ->withPivot('enabled');
            },
            'payments' => function ($query)
            {
                $query->select(Fields::get('payments'));
            }
        ]);

        // Check the rooms number
        if ($voucher->rooms->count() <= 1) {
            flash(trans('vouchers.impossible.room.change'))->info();

            return redirect()->route('vouchers.show', [
                'id' => Hashids::encode($voucher->id)
            ]);
        }

        // Check the enabled rooms number
        if ($voucher->rooms->where('pivot.enabled', true)->count() <= 1) {
            flash(trans('vouchers.impossible.room.change'))->info();

            return redirect()->route('vouchers.show', [
                'id' => Hashids::encode($voucher->id)
            ]);
        }

        // Check the guests number
        if ($voucher->guests->count() <= 1) {
            flash(trans('vouchers.impossible.room.change'))->info();

            return redirect()->route('vouchers.show', [
                'id' => Hashids::encode($voucher->id)
            ]);
        }

        // Get the guest
        $guest = $voucher->guests->where('id', Id::get($guest))->first();

        // Check if guest isn't in hotel and if the guest is inactive in the current voucher
        if ($guest->status == false and $guest->pivot->active == false) {
            flash(trans('vouchers.impossible.room.change'))->info();

            return redirect()->route('vouchers.show', [
                'id' => Hashids::encode($voucher->id)
            ]);
        }

        // The current guest room
        $room = $guest->rooms->first();
        $customer = Customer::get($voucher);

        return view('app.vouchers.change-guest-room', compact('voucher', 'customer', 'guest', 'room'));
    }

    /**
     * Remove guests to voucher.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id
     * @param int $guest
     * @return \Illuminate\Http\Response
     */
    public function changeGuestRoom(ChangeGuestRoom $request, $id, $guest)
    {
        $id = Id::get($id);
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', $id)
            ->where('open', true)
            ->where('status', true)
            ->first(Fields::parsed('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        $voucher->load([
            'hotel' => function ($query) {
                $query->select(Fields::get('hotels'));
            },
            'guests' => function ($query) {
                $query->select(Fields::parsed('guests'))
                    ->withPivot('main', 'active');
            },
            'guests.rooms' => function ($query) use ($id) {
                $query->select(Fields::parsed('rooms'))
                    ->wherePivot('voucher_id', $id);
            },
            'company' => function ($query) {
                $query->select(Fields::get('companies'));
            },
            'rooms' => function ($query) use ($id) {
                $query->select(Fields::parsed('rooms'));
            }
        ]);

        if ($voucher->rooms->count() <= 1) {
            flash(trans('vouchers.impossible.room.change'))->info();

            return redirect()->route('vouchers.show', [
                'id' => Hashids::encode($voucher->id)
            ]);
        }

        if ($voucher->guests->count() <= 1) {
            flash(trans('vouchers.impossible.room.change'))->info();

            return redirect()->route('vouchers.show', [
                'id' => Hashids::encode($voucher->id)
            ]);
        }

        // The guest
        $guest = $voucher->guests->where('id', Id::get($guest))->first();

        // Check if guest is active in hotel and the current voucher
        if ($guest->status == false and $guest->pivot->active == false) {
            flash(trans('vouchers.impossible.room.change'))->info();

            return redirect()->route('vouchers.show', [
                'id' => Hashids::encode($voucher->id)
            ]);
        }

        // Detach current guest room
        $guest->rooms()->detach($guest->rooms()->first()->id);

        // The room to assign to guest
        $room = $voucher->rooms->where('number', $request->number)->first();

        // Attach the selected room to guest
        $guest->rooms()->attach($room->id, [
            'voucher_id' => $voucher->id
        ]);

        flash(trans('common.successful'))->success();

        return redirect()->route('vouchers.show', [
            'id' => Hashids::encode($voucher->id)
        ]);
    }

    /**
     * Show the form for adding products to voucher.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function products($id = '')
    {
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->where('reservation', false)
            ->with([
                'rooms' => function ($query) {
                    $query->select('id', 'number');
                },
                'guests' => function ($query) {
                    $query->select(Fields::parsed('guests'))
                        ->withPivot('main', 'active');
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
            ])->first(Fields::parsed('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        $products = Product::where('user_id', Id::parent())
            ->where('hotel_id', $voucher->hotel->id)
            ->where('quantity', '>', 0)
            ->where('status', true)
            ->get(Fields::get('products'));

        if ($products->isEmpty()) {
            flash('No hay productos disponibles')->info();

            return redirect()->route('vouchers.show', [
                'id' => Hashids::encode($voucher->id)
            ]);
        }

        $customer = Customer::get($voucher);

        return view('app.vouchers.add-products', compact('voucher', 'products', 'customer'));
    }

    /**
     * Store the product values to voucher.
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
                $voucher = Voucher::where('user_id', Id::parent())
                    ->where('id', Id::get($id))
                    ->where('open', true)
                    ->where('status', true)
                    ->where('reservation', false)
                    ->with([
                        'hotel' => function ($query) {
                            $query->select(Fields::get('hotels'));
                        }
                    ])->first(Fields::parsed('vouchers'));

                $product = Product::where('user_id', Id::parent())
                    ->where('id', Id::get($request->product))
                    ->where('hotel_id', $voucher->hotel->id)
                    ->where('quantity', '>', 0)
                    ->where('status', true)
                    ->first(Fields::get('products'));

                $value = (int) $request->quantity * $product->price;

                $voucher->products()->attach(
                    $product->id,
                    [
                        'quantity' => $request->quantity,
                        'value' => $value,
                        'created_at' => Carbon::now()->toDateTimeString()
                    ]
                );

                $voucher->subvalue += $value;
                $voucher->value += $value;
                $voucher->save();

                $product->quantity -= $request->quantity;
                $product->save();

                $status = true;
            } catch (\Throwable $e) {
                Storage::append('voucher.log', $e->getMessage());
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
     * Remove a product from the voucher.
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
                $voucher = Voucher::where('user_id', Id::parent())
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
                    ])->first(Fields::parsed('vouchers'));

                $product = $voucher->products->first();

                $product->quantity += $product->pivot->quantity;
                $product->save();

                $voucher->subvalue -= $product->pivot->value;
                $voucher->value -= $product->pivot->value;
                $voucher->save();

                $voucher->products()
                    ->wherePivot('id', Id::get($record))
                    ->detach($product->id);

                $status = true;
            } catch (\Throwable $e) {
                Storage::append('voucher.log', $e->getMessage());
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
     * Show the form to add services to voucher.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function showFormToAddServices($id, $type = 'all')
    {
        $type = Input::clean($type);

        if (!in_array($type, ['all', 'dining'])) {
            abort(400);
        }

        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->where('reservation', false)
            ->with([
                'guests' => function ($query) {
                    $query->select(Fields::parsed('guests'))
                        ->withPivot('main', 'active');
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
            ->first(Fields::parsed('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        // Check if is dining service
        $serviceType = $type == 'dining' ? true : false;

        $services = Service::where('user_id', Id::parent())
            ->where('hotel_id', $voucher->hotel->id)
            ->whereStatus(true)
            ->where('is_dining_service', $serviceType)
            ->get(Fields::get('services'));

        if ($services->isEmpty()) {
            flash('No hay servicios disponibles')->info();

            return redirect()->route('vouchers.show', [
                'id' => Hashids::encode($voucher->id)
            ]);
        }

        $customer = Customer::get($voucher);

        return view('app.vouchers.add-services', compact('voucher', 'services', 'customer'));
    }

    /**
     * Store the services values to voucher.
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
                $voucher = Voucher::where('user_id', Id::parent())
                    ->where('id', Id::get($id))
                    ->where('open', true)
                    ->where('status', true)
                    ->where('reservation', false)
                    ->with([
                        'hotel' => function ($query) {
                            $query->select(Fields::get('hotels'));
                        }
                    ])->first(Fields::parsed('vouchers'));

                $service = Service::where('user_id', Id::parent())
                    ->where('id', Id::get($request->service))
                    ->where('hotel_id', $voucher->hotel->id)
                    ->whereStatus(true)
                    ->first(Fields::get('services'));

                $value = (int) $request->quantity * $service->price;

                $voucher->services()->attach(
                    $service->id,
                    [
                        'quantity' => $request->quantity,
                        'value' => $value,
                        'created_at' => Carbon::now()->toDateTimeString()
                    ]
                );

                $voucher->subvalue += $value;
                $voucher->value += $value;
                $voucher->update();

                $status = true;
            } catch (\Throwable $e) {
                Storage::append('voucher.log', $e->getMessage());
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
     * Remove a service from the voucher.
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
                $voucher = Voucher::where('user_id', Id::parent())
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
                    ])->first(Fields::parsed('vouchers'));

                $service = $voucher->services->first();

                $voucher->subvalue -= $service->pivot->value;
                $voucher->value -= $service->pivot->value;
                $voucher->save();

                $voucher->services()
                    ->wherePivot('id', Id::get($record))
                    ->detach($service->id);

                $status = true;
            } catch (\Throwable $e) {
                Storage::append('voucher.log', $e->getMessage());
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
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->with([
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
            ])
            ->first(Fields::parsed('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        $customer = Customer::get($voucher);

        return view('app.vouchers.search-companies', compact('voucher', 'customer'));
    }

    /**
     * Attach a company to voucher.
     *
     * @param string $id
     * @param string $company
     * @return \Illuminate\Http\Response
     */
    public function addCompanies($id, $company)
    {
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->first(['id']);

        $company = Company::where('user_id', Id::parent())
            ->where('id', Id::get($company))
            ->first(Fields::get('companies'));

        if (empty($voucher) or empty($company)) {
            abort(404);
        }

        $voucher->company()->associate($company->id);

        if ($voucher->update()) {
            flash(trans('common.successful'))->success();

            return redirect()->route('vouchers.show', [
                'id' => $id
            ]);
        }

        flash(trans('common.error'))->error();

        return redirect()->route('vouchers.show', [
            'id' => $id
        ]);
    }

    /**
     * Detach a company from voucher.
     *
     * @param string $id
     * @param string $company
     * @return \Illuminate\Http\Response
     */
    public function removeCompany($id, $company)
    {
        $voucher = Voucher::where('user_id', Id::parent())
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

        if (empty($voucher) or empty($voucher->company)) {
            abort(404);
        }

        $voucher->company()->dissociate();

        if ($voucher->update()) {
            flash(trans('common.successful'))->success();

            return redirect()->route('vouchers.show', [
                'id' => $id
            ]);
        }

        flash(trans('common.error'))->error();

        return redirect()->route('vouchers.show', [
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
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->where('reservation', false)
            ->with([
                'rooms' => function ($query) {
                    $query->select('id');
                },
                'rooms.guests' => function ($query) use ($id) {
                    $query->select('id', 'name', 'last_name')
                        ->wherePivot('voucher_id', Id::get($id));
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
            ])->first(Fields::parsed('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        if ($voucher->rooms->isEmpty()) {
            flash(trans('vouchers.firstStep'))->info();

            return redirect()->route('vouchers.rooms.add', [
                'id' => Hashids::encode($voucher->id)
            ]);
        }

        if ($voucher->guests->isEmpty()) {
            flash(trans('vouchers.withoutGuests'))->info();

            return redirect()->route('vouchers.show', [
                'id' => Hashids::encode($voucher->id)
            ]);
        }

        $customer = Customer::get($voucher);

        return view('app.vouchers.search-vehicles', compact('voucher', 'customer'));
    }

    /**
     * Attach a vehicle to guest voucher.
     *
     * @param string $id
     * @param string $vehicleId
     * @param string $guestId
     * @return \Illuminate\Http\Response
     */
    public function addVehicle($id, $vehicleId, $guestId)
    {
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->where('reservation', false)
            ->with([
                'guests' => function ($query) {
                    $query->select(Fields::parsed('guests'))
                        ->withPivot('main', 'active');
                },
                'guests.vehicles' => function ($query) use ($id) {
                    $query->select(Fields::parsed('vehicles'))
                        ->wherePivot('voucher_id', Id::get($id));
                }
            ])->first(Fields::parsed('vouchers'));

        $vehicle = Vehicle::where('user_id', Id::parent())
            ->where('id', Id::get($vehicleId))
            ->first(Fields::get('vehicles'));

        if (empty($voucher) or empty($vehicle)) {
            abort(404);
        }

        if ($voucher->guests->where('id', Id::get($guestId))->first()->vehicles->isNotEmpty()) {
            flash(trans('vouchers.hasVehicles'))->error();

            return redirect()->route('vouchers.vehicles.search', [
                'id' => Hashids::encode($voucher->id)
            ]);
        }

        $existingVehicle = null;
        foreach ($voucher->guests as $guest) {
            if ($guest->vehicles->where('id', Id::get($vehicleId))->count() == 1) {
                $existingVehicle = $guest->vehicles->where('id', Id::get($vehicleId))->first();
            }
        }

        if (empty($existingVehicle)) {
            $vehicle->guests()->attach($voucher->guests->where('id', Id::get($guestId))->first()->id, [
                'voucher_id' => $voucher->id,
                'created_at' => Carbon::now()->toDateTimeString()
            ]);

            flash(trans('common.successful'))->success();

            return redirect()->route('vouchers.show', [
                'id' => Hashids::encode($voucher->id)
            ]);
        }

        flash(trans('vouchers.vehicleAttached'))->error();

        return redirect()->route('vouchers.vehicles.search', [
            'id' => Hashids::encode($voucher->id)
        ]);
    }

    /**
     * Detach a vehicle from guest voucher.
     *
     * @param string $id
     * @param string $vehicle
     * @param string $guest
     * @return \Illuminate\Http\Response
     */
    public function removeVehicle($id, $vehicle, $guest)
    {
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->where('reservation', false)
            ->with([
                'guests' => function ($query) use ($guest) {
                    $query->select(Fields::parsed('guests'))
                        ->where('id', Id::get($guest));
                }
            ])->first(Fields::parsed('vouchers'));

        $vehicle = Vehicle::where('user_id', Id::parent())
            ->where('id', Id::get($vehicle))
            ->first(Fields::get('vehicles'));

        if (empty($voucher) or empty($vehicle)) {
            abort(404);
        }

        $vehicle->guests()
            ->wherePivot('voucher_id', $voucher->id)
            ->detach($voucher->guests->first()->id);

        flash(trans('common.successful'))->success();

        return redirect()->route('vouchers.show', [
            'id' => Hashids::encode($voucher->id)
        ]);
    }

    /**
     * Show form to create additional value to the voucher.
     *
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function createAdditional($id)
    {
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->where('reservation', false)
            ->with([
                'rooms' => function ($query) {
                    $query->select('id');
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
            ])->first(Fields::parsed('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        $customer = Customer::get($voucher);

        return view('app.vouchers.add-additional', compact('voucher', 'customer'));
    }

    /**
     * Store additional value to the voucher.
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
                $voucher = Voucher::where('user_id', Id::parent())
                    ->where('id', Id::get($id))
                    ->where('open', true)
                    ->where('status', true)
                    ->where('reservation', false)
                    ->first(Fields::parsed('vouchers'));

                // Create new additional for the voucher
                $additional = new Additional();
                $additional->description = $request->description;
                $additional->value = (float) $request->value;
                $additional->billable = true;
                $additional->voucher()->associate($voucher->id);
                $additional->save();

                // Increment voucher values
                $voucher->subvalue += $additional->value;
                $voucher->value += $additional->value;
                $voucher->save();

                $status = true;
            } catch (\Throwable $e) {
                Storage::append('voucher.log', $e->getMessage());
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
     * Remove additional value to the voucher.
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
                $voucher = Voucher::where('user_id', Id::parent())
                    ->where('id', Id::get($id))
                    ->where('open', true)
                    ->where('status', true)
                    ->where('reservation', false)
                    ->first(Fields::parsed('vouchers'));

                if (empty($voucher)) {
                    abort(404);
                }

                // Query the additional to remove
                $additional = Additional::where('voucher_id', $voucher->id)
                    ->where('id', Id::get($additional))
                    ->first(['id', 'value', 'billable','voucher_id']);

                // Check is a billable additional
                if ($additional->billable) {
                    // Subtract the additional values from the voucher
                    $voucher->subvalue -= $additional->value;
                    $voucher->value -= $additional->value;
                    $voucher->save();
                }

                // Destroy the additional
                $additional->delete();

                $status = true;
            } catch (\Throwable $e) {
                Storage::append('voucher.log', $e->getMessage());
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
     * Show form to add external service to the voucher.
     *
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function addExternalService($id)
    {
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->where('reservation', false)
            ->with([
                'rooms' => function ($query) {
                    $query->select('id');
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
            ])->first(Fields::parsed('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        $customer = Customer::get($voucher);

        return view('app.vouchers.add-external', compact('voucher', 'customer'));
    }

    /**
     * Store additional value to the voucher.
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
                $voucher = Voucher::where('user_id', Id::parent())
                    ->where('id', Id::get($id))
                    ->where('open', true)
                    ->where('status', true)
                    ->where('reservation', false)
                    ->first(Fields::parsed('vouchers'));

                // Create new additional for the voucher
                $additional = new Additional();
                $additional->description = $request->description;
                $additional->value = (float) $request->value;
                $additional->billable = false;
                $additional->voucher()->associate($voucher->id);
                $additional->save();

                $status = true;
            } catch (\Throwable $e) {
                Storage::append('voucher.log', $e->getMessage());
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
     * Close an open voucher.
     * The open status is by default in true value.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function close($id)
    {
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('payment_status', false)
            ->where('status', true)
            ->with([
                'rooms' => function ($query) {
                    $query->select(Fields::parsed('rooms'));
                },
                'guests' => function ($query) {
                    $query->select(Fields::parsed('guests'));
                }
            ])->first(Fields::parsed('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        $status = false;

        DB::transaction(function () use (&$status, &$voucher) {
            try {
                $voucher->open = false;

                if ($voucher->save()) {
                    // Change Room status to cleaning
                    Room::whereIn('id', $voucher->rooms->pluck('id')->toArray())->update(['status' => '2']);

                    // Change Guest status to false: Guest is not in hotel
                    if ($voucher->guests->isNotEmpty()) {
                        Guest::whereIn('id', $voucher->guests->pluck('id')->toArray())
                            ->whereDoesntHave('vouchers', function (Builder $query) use ($voucher) {
                                $query->where('open', true)
                                    ->where('status', true)
                                    ->where('reservation', false)
                                    ->where('created_at', '>', $voucher->created_at);
                            })->update(['status' => false]);
                    }

                    $status = true;
                }
            } catch (\Throwable $e) {
                Storage::append('voucher.log', $e->getMessage());
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
     * Open a closed voucher.
     * Only a manager could open a closed voucher.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function open($id)
    {
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', false)
            ->where('status', true)
            ->first(Fields::parsed('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        $voucher->open = true;

        if ($voucher->save()) {
            flash(trans('common.successful'))->success();

            return back();
        }

        flash(trans('common.error'))->error();

        return back();
    }

    /**
     * Close Payments in a closed voucher.
     * The open status is by default in true value.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function closePayment($id)
    {
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('status', true)
            ->where('payment_status', false)
            ->with([
                'payments' => function ($query)
                {
                    $query->select(Fields::get('payments'));
                }
            ])->first(Fields::parsed('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        if ($voucher->open) {
            flash(trans('vouchers.isOpen'))->info();

            return back();
        }

        if ((float) $voucher->value > $voucher->payments->sum('value')) {
            flash(trans('payments.incomplete'))->info();

            return back();
        }

        $status = false;

        DB::transaction(function () use (&$status, &$voucher) {
            try {
                $voucher->payment_status = true;

                if ($voucher->save()) {
                    $status = true;
                }
            } catch (\Throwable $e) {
                Storage::append('voucher.log', $e->getMessage());
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
     * Open a closed voucher.
     * Only a manager could open a closed voucher.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function registerAsLoss($id)
    {
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('status', true)
            ->where('payment_status', false)
            ->where('losses', false)
            ->with([
                'rooms' => function ($query) {
                    $query->select(Fields::parsed('rooms'));
                },
                'guests' => function ($query) {
                    $query->select(Fields::parsed('guests'));
                },
                'payments' => function ($query)
                {
                    $query->select(Fields::get('payments'));
                }
            ])->first(Fields::parsed('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        if ($voucher->payment_method) {
            flash(trans('payments.complete'))->info();

            return back();
        }

        if ((float) $voucher->value == $voucher->payments->sum('value')) {
            flash(trans('payments.complete'))->info();

            return back();
        }

        $status = false;

        DB::transaction(function () use (&$status, &$voucher) {
            try {
                $voucher->open = false;
                $voucher->losses = true;

                if ($voucher->save()) {
                    // Change Room status to cleaning
                    Room::whereIn('id', $voucher->rooms->pluck('id')->toArray())->update(['status' => '2']);

                    // Change Guest status to false: Guest is not in hotel
                    if ($voucher->guests->isNotEmpty()) {
                        Guest::whereIn('id', $voucher->guests->pluck('id')->toArray())
                            ->whereDoesntHave('vouchers', function (Builder $query) use ($voucher) {
                                $query->where('open', true)
                                    ->where('status', true)
                                    ->where('reservation', false)
                                    ->where('created_at', '>', $voucher->created_at);
                            })->update(['status' => false]);
                    }

                    $status = true;
                }
            } catch (\Throwable $e) {
                Storage::append('voucher.log', $e->getMessage());
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
     * Show form to change an voucher reservation to check-in reservation.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function takeReservationCheckin($id)
    {
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->where('reservation', true)
            ->with([
                'rooms' => function ($query) {
                    $query->select('id');
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
            ])->first(Fields::parsed('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        $customer = Customer::get($voucher);

        return view('app.vouchers.reservation-checkin', compact('voucher', 'customer'));
    }

    /**
     * Change an voucher reservation to check-in reservation, includes origin and destination route.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function storeReservationCheckin(StoreRoute $request, $id)
    {
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', Id::get($id))
            ->where('open', true)
            ->where('status', true)
            ->where('reservation', true)
            ->first(Fields::parsed('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        $voucher->origin = $request->get('origin', null);
        $voucher->destination = $request->get('destination', null);
        $voucher->reservation = false;

        if ($voucher->save()) {
            flash(trans('common.successful'))->success();

            return redirect()->route('vouchers.guests.search', [
                'id' => Hashids::encode($voucher->id)
            ]);
        }

        flash(trans('common.error'))->error();

        return redirect()->route('vouchers.show', [
            'id' => Hashids::encode($voucher->id)
        ]);
    }

    /**
     * Change an voucher reservation to check-in reservation, includes origin and destination route.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function showFormToProcess()
    {
        // Query hotels with vouchers to process
        $hotels = Hotel::query();

        // Only hotels of parent user
        $hotels->where('user_id', Id::parent());

        // Check if is receptionist
        if (auth()->user()->hasRole('receptionist')) {
            $hotels->where('hotel_id', auth()->user()->headquarters()->first()->id);
        }

        // Check if hotels have vouchers to process
        $hotels->whereHas('vouchers', function ($query) {
            $query->where('open', true)
                ->where('status', true)
                ->where('reservation', false)
                ->where('payment_status', false)
                ->where('losses', false);
        });

        // Load vouchers with relateds
        $hotels->with([
                'vouchers' => function ($query) {
                    $query->select(Fields::parsed('vouchers'))
                        ->where('user_id', Id::parent())
                        ->where('open', true)
                        ->where('status', true)
                        ->where('reservation', false)
                        ->where('payment_status', false)
                        ->where('losses', false);
                },
                'vouchers.guests' => function ($query) {
                    $query->select(['id', 'name', 'last_name'])
                        ->wherePivot('main', true);
                },
                'vouchers.rooms' => function ($query)
                {
                    $query->select(Fields::parsed('rooms'))
                        ->wherePivot('enabled', true)
                        ->withPivot('quantity', 'discount', 'subvalue', 'taxes', 'value', 'start', 'end', 'price', 'enabled');
                },
                'vouchers.company' => function ($query) {
                    $query->select(['id', 'tin', 'business_name']);
                },
                'vouchers.payments' => function ($query)
                {
                    $query->select(Fields::get('payments'));
                }
            ]);

        // Get results
        $hotels = $hotels->get(Fields::get('hotels'));

        if ($hotels->isEmpty()) {
            flash(trans('vouchers.nothingToProcess'))->info();

            return back();
        }

        // Hashing IDs before convert to JSON format
        $hotels = $this->prepareData($hotels);

        return view('app.vouchers.process', compact('hotels'));
    }

    /**
     * Hashing all IDs, model and relationships, before convert to JSON format.
     *
     * @param  \Illuminate\Support\Collection  $hotels
     * @return \Illuminate\Support\Collection  $hotels
     */
    public function prepareData(Collection $hotels)
    {
        $hotels = $hotels->map(function($hotel) {
            $hotel->user_id = Hashids::encode($hotel->user_id);
            $hotel->main_hotel = $hotel->main_hotel ? Hashids::encode($hotel->main_hotel) : null;

            $hotel->vouchers = $hotel->vouchers->map(function ($voucher) {
                $voucher->user_id = Hashids::encode($voucher->user_id);
                $voucher->hotel_id = Hashids::encode($voucher->hotel_id);
                $voucher->company_id = $voucher->company_id ? Hashids::encode($voucher->company_id) : null;

                $voucher->guests = $voucher->guests->map(function ($guest)
                {
                    $guest->pivot->voucher_id = Hashids::encode($guest->pivot->voucher_id);
                    $guest->pivot->guest_id = Hashids::encode($guest->pivot->guest_id);

                    return $guest;
                });

                $voucher->rooms = $voucher->rooms->map(function ($room)
                {
                    $room->user_id = Hashids::encode($room->user_id);
                    $room->hotel_id = Hashids::encode($room->hotel_id);
                    $room->pivot->voucher_id = Hashids::encode($room->pivot->voucher_id);
                    $room->pivot->room_id = Hashids::encode($room->pivot->room_id);

                    return $room;
                });

                $voucher->payments = $voucher->payments->map(function ($payment)
                {
                    $payment->voucher_id = Hashids::encode($payment->voucher_id);

                    return $payment;
                });

                return $voucher;
            });

            return $hotel;
        });

        return $hotels;
    }

    /**
     * Add a night to the room associated with the voucher and calculate the new values
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function process(VouchersProcessing $request)
    {
        // All processed vouchers will be placed here
        // So the processed vouchers will be returned as JSON response
        $processed = collect();

        DB::transaction(function () use (&$processed, $request) {
            try {
                $vouchers = Voucher::where('user_id', Id::parent())
                    ->where('hotel_id', Id::get($request->hotel))
                    ->whereIn('number', $request->numbers)
                    ->where('open', true)
                    ->where('status', true)
                    ->where('reservation', false)
                    ->where('payment_status', false)
                    ->where('losses', false)
                    ->with([
                        'rooms' => function ($query)
                        {
                            $query->select(Fields::parsed('rooms'))
                                ->wherePivot('enabled', true)
                                ->withPivot('quantity', 'discount', 'subvalue', 'taxes', 'value', 'start', 'end', 'price', 'enabled');
                        }
                    ])->get(Fields::parsed('vouchers'));

                if ($vouchers->isNotEmpty()) {
                    $vouchers->each(function ($voucher) use (&$processed)
                    {
                        $voucher->rooms->each(function ($room) use (&$processed, $voucher)
                        {
                            // Dates
                            $start = Carbon::createFromFormat('Y-m-d', $room->pivot->start);
                            $end = Carbon::createFromFormat('Y-m-d', $room->pivot->end);
                            $tomorrow = Carbon::tomorrow();

                            // Check if the room is enabled to changes
                            if ($room->pivot->enabled and $end->lessThan($tomorrow)) {
                                // Calculate diff in days
                                $diff = $start->diffInDays($end);

                                // Check if the current quantity is same diff in days
                                // This code check integrity of calculations
                                if ($diff === (int) $room->pivot->quantity) {
                                    // Add a day to end date
                                    $newEnd = $end->copy()->addDays(1);

                                    // New quantity to add
                                    $quantity = $end->diffInDays($newEnd);

                                    // Adding the new values
                                    $discount = ((float) $room->price - (float) $room->pivot->price) * $quantity;
                                    $taxes = ((float) $room->pivot->price * $room->tax) * $quantity;
                                    $subvalue = (float) $room->pivot->price * $quantity;
                                    $value = $subvalue + $taxes;

                                    $voucher->rooms()->updateExistingPivot(
                                        $room,
                                        [
                                            'quantity' => $room->pivot->quantity + $quantity,
                                            'discount' => $room->pivot->discount + $discount,
                                            'subvalue' => $room->pivot->subvalue + $subvalue,
                                            'taxes' => $room->pivot->taxes + $taxes,
                                            'value' => $room->pivot->value + $value,
                                            'end' => $newEnd->toDateString()
                                        ]
                                    );

                                    $voucher->discount += $discount;
                                    $voucher->subvalue += $subvalue;
                                    $voucher->taxes += $taxes;
                                    $voucher->value += $value;

                                    if ($voucher->save()) {
                                        $processed->push($voucher);
                                    }
                                }
                            }
                        });
                    });
                }
            } catch (\Throwable $e) {
                Storage::append('voucher.log', $e->getMessage());
            }
        });

        if ($processed->isEmpty()) {
            $processed = [];
        } else {
            $processed = $processed->pluck('number')->toArray();
        }

        return response()->json([
            'processed' => $processed
        ]);
    }

    /**
     * Export a voucher to PDF.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function export($id)
    {
        $id = Id::get($id);
        $voucher = Voucher::where('user_id', Id::parent())
            ->where('id', $id)
            ->where('status', true)
            ->first(Fields::parsed('vouchers'));

        if (empty($voucher)) {
            abort(404);
        }

        $voucher->load([
            'guests' => function ($query) {
                $query->select(Fields::parsed('guests'))
                    ->withPivot('main', 'active');
            },
            'company' => function ($query) {
                $query->select(Fields::get('companies'));
            },
            'rooms' => function ($query) {
                $query->select(Fields::parsed('rooms'))
                    ->withPivot('quantity', 'discount', 'subvalue', 'taxes', 'value', 'start', 'end', 'price', 'enabled');
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
                $query->select(['id', 'description', 'billable','value', 'voucher_id', 'created_at'])
                    ->where('billable', true);
            },
            'payments' => function ($query)
            {
                $query->select(Fields::get('payments'));
            },
            'props' => function ($query) {
                $query->select(Fields::parsed('props'))
                    ->withPivot('quantity', 'value', 'created_at');
            },
        ]);

        $customer = Customer::get($voucher);
        $pages = $this->prepareItems($voucher);

        $view = view('app.vouchers.exports.template', compact('voucher', 'customer', 'pages'))->render();

        $pdf = App::make('snappy.pdf.wrapper');
        $pdf->setOption('enable-javascript', true);
        $pdf->setOption('images', true);
        $pdf->setOption('enable-smart-shrinking', true);
        $pdf->setOption('margin-top', 3);
        $pdf->setOption('margin-bottom', 3);
        $pdf->setOption('margin-left', 1);
        $pdf->setOption('margin-right', 1);
        $pdf->loadHTML($view);

        return $pdf->download('voucher.pdf');
    }

    public function prepareItems(Voucher $voucher)
    {
        $items = collect();

        foreach ($voucher->rooms as $room) {
            $items->push($room);
        }

        foreach ($voucher->services as $service) {
            $items->push($service);
        }

        foreach ($voucher->products as $product) {
            $items->push($product);
        }

        foreach ($voucher->additionals as $additional) {
            $items->push($additional);
        }

        foreach ($voucher->props as $prop) {
            $items->push($prop);
        }

        return $this->chunkItems($items);
    }

    public function chunkItems(Collection $items)
    {
        $pages = $this->calculatePages($items);
        $chunkedItems = [];

        // Will be used the collection function named 'splice'
        // The splice method removes and returns a slice of items starting at the specified index
        for ($i=1; $i <= $pages; $i++) {
            // Check if this is the first page
            if ($i == 1) {
                // The first page receives only 13 items of 18
                $chunkedItems[$i] = $items->splice(0, 13);
            } else {
                // Check if this is the last page
                if ($i == $pages) {
                    // The last page receives only 10 items of 18
                    $chunkedItems[$i] = $items->splice(0, 10);
                } else {
                    // This is a intermediate page, total items, 18 items
                    $chunkedItems[$i] = $items->splice(0, 18);
                }
            }
        }

        return $chunkedItems;
    }

    /**
     * Calculate pages total
     *
     * @return \Illuminate\Support\Collection  $items
     * @return int
     */
    public function calculatePages(Collection $items): int
    {
        // The maximun quantity per page
        $itemsPerPage = 18;

        // The space for the voucher header and signature
        // 5 items for the header
        // 7 items for the signature, this section includes the questions section
        $reservedSpace = 12;

        // Calculate the total items, includes the reserved space (header, signature)
        // This quantity is to calculate the pages
        $quantity = $items->count() + $reservedSpace;

        // The pages are round fractions up using ceil function
        $pages = (int) ceil($quantity / $itemsPerPage);

        return $pages;
    }
}
