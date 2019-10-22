<?php

namespace App\Http\Controllers;

use App\User;
use App\Welkome\Room;
use App\Welkome\Hotel;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRoom;
use App\Http\Requests\UpdateRoom;
use Vinkla\Hashids\Facades\Hashids;
use App\Helpers\{Id, Input, Fields};

# TODO: Habilitar y deshabilitar habitaciones
class RoomController extends Controller
{
    /**
     * Display a listing of the resource for admin users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hotels = $this->getHotels();


        $hotels = $hotels->map(function ($hotel, $index)
        {
            $hotel->user_id = Hashids::encode($hotel->user_id);
            $hotel->rooms = $hotel->rooms->map(function ($room)
            {
                $room->hotel_id = Hashids::encode($room->hotel_id);
                $room->user_id = Hashids::encode($room->user_id);

                return $room;
            });

            return $hotel;
        });
        // TODO: Agregar desplegable de hoteles al index de rooms
        return view('app.rooms.index', compact('hotels'));
    }

    /**
     * Return hotel list to attach to invoice.
     *
     * @return  \Illuminate\Support\Collection
     */
    private function getHotels()
    {
        if (auth()->user()->hasRole('manager')) {
            $hotels = Hotel::where('user_id', Id::parent())
                ->where('status', true)
                ->with([
                    'rooms' => function ($query) {
                        $query->select(Fields::get('rooms'));
                    }
                ])->get(Fields::get('hotels'));

            return $hotels;
        }

        $user = auth()->user()->load([
            'headquarters' => function ($query)
            {
                $query->select(Fields::parsed('hotels'))
                    ->where('status', true);
            },
            'headquarters.rooms' => function ($query) {
                $query->select(Fields::parsed('rooms'));
            }
        ]);

        return $user->headquarters;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $hotels = User::find(Id::parent(), ['id'])
            ->hotels()
            ->get(Fields::get('hotels'));

        return view('app.rooms.admin.create', compact('hotels'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRoom $request)
    {
        $room = new Room();
        $room->floor = (int) $request->floor;
        $room->number = $request->number;
        $room->price = (float) $request->price;
        $room->min_price = (float) $request->min_price;
        $room->description = $request->description;
        $room->status = '1';
        $room->capacity = (int) $request->capacity;
        $room->hotel()->associate(Id::get($request->hotel));

        if (in_array((int) $request->tax_status, [1,2])) {
            $room->tax_status = $request->tax_status;
            $room->tax = (float) $request->tax;
        }

        $room->user()->associate(Id::parent());
        $room->is_suite = (int) $request->type;

        if ($room->save()) {
            flash(trans('common.createdSuccessfully'))->success();

            return redirect()->route('rooms.index');
        }

        flash(trans('common.error'))->error();

        return redirect()->route('rooms.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $room = User::find(Id::parent(), ['id'])->rooms()
            ->where('id', Id::get($id))
            ->first(Fields::get('rooms'));

        if (empty($room)) {
            abort(404);
        }

        $room->load([
            'assets' => function ($query)
            {
                $query->select('id', 'number', 'description', 'brand', 'model', 'reference');
            },
            'products' => function ($query)
            {
                $query->select('id', 'description', 'brand', 'reference', 'price');
            },
        ]);

        return view('app.rooms.show', compact('room'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $room = User::find(Id::parent(), ['id'])->rooms()
            ->where('id', Id::get($id))
            ->with([
                'hotel' => function ($query)
                {
                    $query->select(['id', 'business_name']);
                }
            ])->first(Fields::get('rooms'));

        if (empty($room)) {
            abort(404);
        }

        return view('app.rooms.admin.edit', compact('room'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRoom $request, $id)
    {
        $room = User::find(Id::parent(), ['id'])->rooms()
            ->where('id', Id::get($id))
            ->first(Fields::get('rooms'));

        if (empty($room)) {
            abort(404);
        }

        $room->price = $request->price;
        $room->description = $request->description;
        $room->min_price = (float) $request->min_price;
        $room->description = $request->description;
        $room->status = '1';
        $room->capacity = (int) $request->capacity;

        if (in_array((int) $request->tax_status, [1,2])) {
            $room->tax_status = $request->tax_status;
            $room->tax = (float) $request->tax;
        } else {
            $room->tax_status = "0";
            $room->tax = 0.0;
        }

        $room->is_suite = (int) $request->type;

        if ($room->update()) {
            flash(trans('common.updatedSuccessfully'))->success();

            return redirect()->route('rooms.show', [
                'room' => Hashids::encode($room->id)
            ]);
        }

        flash(trans('common.error'))->error();

        return redirect()->route('rooms.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $room = User::find(Id::parent(), ['id'])->rooms()
            ->where('id', Id::get($id))
            ->first(Fields::get('rooms'));

        if (empty($room)) {
            abort(404);
        }

        $room->load([
            'invoices' => function ($query)
            {
                $query->select('id');
            },
        ]);

        if ($room->invoices->count() > 0) {
            $room->status = '3';

            if ($room->update()) {
                flash(trans('rooms.wasDisabled'))->success();

                return redirect()->route('rooms.show', [
                    'room' => Hashids::encode($room->id)
                ]);
            }
        } else {
            if ($room->delete()) {
                flash(trans('common.deletedSuccessfully'))->success();

                return redirect()->route('rooms.index');
            }
        }

        flash(trans('common.error'))->error();

        return redirect()->route('rooms.index');
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
            return redirect()->route('rooms.index');
        }

        $rooms = User::find(Id::parent(), ['id'])->rooms()
            ->whereLike(['number', 'description'], $query)
            ->paginate(20, Fields::get('rooms'));

        return view('app.rooms.admin.search', compact('rooms', 'query'));
    }

    /**
     * Return a rooms list by hotel ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function listByHotel(Request $request)
    {
        if ($request->ajax()) {
            $rooms = Room::where('hotel_id', Id::get($request->hotel))
                ->where('status', '1') // It is free
                ->get(Fields::get('rooms'));

            $rooms = $rooms->map(function ($room, $index)
            {
                $room->hotel_id = Hashids::encode($room->hotel_id);
                $room->user_id = Hashids::encode($room->user_id);

                return $room;
            });

            return response()->json([
                'rooms' => $rooms->toJson()
            ]);
        }

        abort(404);
    }

    /**
     * Return the price and min price of a room.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function price(Request $request)
    {
        if ($request->ajax()) {
            $room = Room::where('hotel_id', Id::get($request->hotel))
                ->where('number', $request->number)
                ->where('status', '1') // It is free
                ->first(Fields::get('rooms'));

            return response()->json([
                'price' => $room->price,
                'min_price' => $room->min_price
            ]);
        }

        abort(404);
    }
}
