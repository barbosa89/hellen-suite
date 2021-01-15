<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\StoreRoom;
use App\Contracts\RoomRepository;
use App\Http\Controllers\Controller;

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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
