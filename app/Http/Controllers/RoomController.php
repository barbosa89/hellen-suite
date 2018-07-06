<?php

namespace App\Http\Controllers;

use App\Welkome\Room;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRoom;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rooms = Room::where('user_id', auth()->user()->id)
            ->paginate(20, ['id', 'number', 'description', 'value', 'status']);
        
        return view('app.rooms.index', compact('rooms'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('app.rooms.create');
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
        $room->number = $request->number;
        $room->value = $request->value;
        $room->description = $request->description;
        $room->status = '1';
        $room->user()->associate(auth()->user()->id);

        if ($room->save()) {
            flash(trans('rooms.successful'))->success();

            return redirect()->route('rooms.index');
        }

        flash(trans('common.error'))->error();

        return redirect()->route('rooms.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Welkome\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function show(Room $room)
    {
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
     * @param  \App\Welkome\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function edit(Room $room)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Welkome\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Room $room)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Welkome\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function destroy(Room $room)
    {
        //
    }
}
