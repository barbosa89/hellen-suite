<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\Room;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRoom;
use App\Http\Requests\UpdateRoom;
use App\Helpers\Chart;
use App\Http\Requests\ChangeRoomStatus;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource for admin users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('app.rooms.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $hotels = User::find(id_parent(), ['id'])
            ->hotels()
            ->get(fields_get('hotels'));

        // Check if is empty
        if ($hotels->isEmpty()) {
            flash(trans('hotels.no.registered'))->info();

            return back();
        }

        return view('app.rooms.create', compact('hotels'));
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
        $room->hotel()->associate(id_decode($request->hotel));

        if ((int) $request->tax_status == 1) {
            $room->tax = (float) $request->tax;
        }

        $room->user()->associate(id_parent());
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
        $room = User::find(id_parent(), ['id'])->rooms()
            ->where('id', id_decode($id))
            ->first(fields_get('rooms'));

        if (empty($room)) {
            abort(404);
        }

        $room->load([
            'hotel' => function ($query)
            {
                $query->select(fields_get('hotels'));
            },
            'assets' => function ($query)
            {
                $query->select(fields_dotted('assets'));
            },
            'products' => function ($query)
            {
                $query->select(fields_dotted('products'));
            },
            'vouchers' => function ($query)
            {
                $query->select(fields_dotted('vouchers'))
                    ->whereYear('vouchers.created_at', date('Y'))
                    ->orderBy('vouchers.created_at', 'DESC')
                    ->withPivot('value');
            }
        ]);

        $data = Chart::create($room->vouchers)
            ->addItemValues()
            ->get();

        return view('app.rooms.show', compact('room', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $room = User::find(id_parent(), ['id'])->rooms()
            ->where('id', id_decode($id))
            ->with([
                'hotel' => function ($query)
                {
                    $query->select(['id', 'business_name']);
                }
            ])->first(fields_get('rooms'));

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
        $room = User::find(id_parent(), ['id'])->rooms()
            ->where('id', id_decode($id))
            ->first(fields_get('rooms'));

        if (empty($room)) {
            abort(404);
        }

        $room->price = $request->price;
        $room->description = $request->description;
        $room->min_price = (float) $request->min_price;
        $room->description = $request->description;
        $room->capacity = (int) $request->capacity;

        if ((int) $request->tax_status == 1) {
            $room->tax = (float) $request->tax;
        } else {
            $room->tax = 0.0;
        }

        $room->is_suite = (int) $request->type;

        if ($room->update()) {
            flash(trans('common.updatedSuccessfully'))->success();

            return redirect()->route('rooms.show', [
                'id' => id_encode($room->id)
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
        $room = User::find(id_parent(), ['id'])->rooms()
            ->where('id', id_decode($id))
            ->first(fields_get('rooms'));

        if (empty($room)) {
            abort(404);
        }

        $room->load([
            'vouchers' => function ($query)
            {
                $query->select('id');
            },
        ]);

        if ($room->vouchers->count() > 0) {
            $room->status = '3';

            if ($room->update()) {
                flash(trans('rooms.wasDisabled'))->success();

                return redirect()->route('rooms.show', [
                    'room' => id_encode($room->id)
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
        $query = param_clean($request->get('query', null));

        if (empty($query)) {
            return redirect()->route('rooms.index');
        }

        $rooms = User::find(id_parent(), ['id'])->rooms()
            ->whereLike(['number', 'description'], $query)
            ->paginate(20, fields_get('rooms'));

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
            $rooms = Room::where('user_id', id_parent())
                ->where('hotel_id', id_decode($request->hotel))
                ->get(fields_get('rooms'));

            $rooms = $rooms->map(function ($room, $index)
            {
                $room->hotel_id = id_encode($room->hotel_id);
                $room->user_id = id_encode($room->user_id);

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
    public function getPrice(Request $request)
    {
        if ($request->ajax()) {
            $room = Room::where('user_id', id_parent())
                ->where('hotel_id', id_decode($request->hotel))
                ->where('number', $request->number)
                ->where('status', '1') // It is free
                ->first(fields_get('rooms'));

            return response()->json([
                'price' => $room->price,
                'min_price' => $room->min_price,
                'tax' => $room->tax
            ]);
        }

        abort(404);
    }

    /**
     * Change room status
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function changeStatus(ChangeRoomStatus $request)
    {
        $room = Room::where('user_id', id_parent())
            ->where('hotel_id', id_decode($request->hotel))
            ->where('id', id_decode($request->room))
            ->first(fields_get('rooms'));

        if (empty($room)) {
            abort(404);
        }

        if ($room->status == '0') {
            abort(403);
        }

        if ($request->status == '1') {
            if (in_array($room->status, ['2', '3', '4'])) {
                $room->status = '1';
            }
        }

        if ($request->status == '3') {
            if (in_array($room->status, ['1', '2', '4'])) {
                $room->status = '3';
            }
        }

        if ($request->status == '4') {
            if (in_array($room->status, ['1', '2', '3'])) {
                $room->status = '4';
            }
        }

        if ($room->save()) {
            return response()->json([
                'result' => true
            ]);
        }

        abort(500);
    }
}
