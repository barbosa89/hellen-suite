<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreRoom;
use App\Contracts\RoomRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangeRoomStatus;

class RoomController extends Controller
{
    public RoomRepository $room;

    public function __construct(RoomRepository $room)
    {
        $this->room = $room;
    }

    /**
     * Display a listing of the resource.
     *
     * @param string $hotel
     * @return \Illuminate\Http\Response
     */
    public function index(string $hotel)
    {
        $rooms = $this->room->all(id_decode($hotel));

        return response()->json([
            'rooms' => $rooms,
        ]);
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

        return response()->json([
            'room' => $room,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id)
    {
        $room = $this->room->find(id_decode($id));

        return response()->json([
            'room' => $room,
        ]);
    }

    /**
     * Change room status
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function toggle(ChangeRoomStatus $request)
    {
        $room = $this->room->toggle(id_decode($request->room), $request->status);

        return response()->json([
            'room' => $room
        ]);
    }
}
