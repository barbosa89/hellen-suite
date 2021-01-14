<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\Room;
use App\Helpers\Chart;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRoom;
use App\Contracts\RoomRepository;
use App\Http\Requests\UpdateRoom;
use App\Http\Requests\ChangeRoomStatus;

class RoomController extends Controller
{
    public RoomRepository $room;

    public function __construct(RoomRepository $room)
    {
        $this->room = $room;
    }

    /**
     * Display a listing of the resources.
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
        $room = $this->room->create($request->hotel_id, $request->validated());

        flash(trans('common.createdSuccessfully'))->success();

        return redirect()->route('rooms.show', ['id' => id_encode($room->id)]);
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
            ->firstOrFail(fields_get('rooms'));

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
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(string $id)
    {
        $room = $this->room->find(id_decode($id));

        return view('app.rooms.edit', compact('room'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRoom $request, string $id)
    {
        $room = $this->room->update(id_decode($id), $request->validated());

        flash(trans('common.updatedSuccessfully'))->success();

        return redirect()->route('rooms.show', [
            'id' => id_encode($room->id)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        if ($this->room->destroy(id_decode($id))) {
            flash(trans('common.deletedSuccessfully'))->success();

            return redirect()->route('rooms.index');
        }

        flash(trans('rooms.cannot.destroy'))->error();

        return redirect()->route('rooms.show', [
            'id' => $id
        ]);
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
