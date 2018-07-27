<?php

namespace App\Http\Controllers;

use App\Welkome\Guest;
use Illuminate\Http\Request;
use App\Helpers\{Input, Fields};

class GuestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Welkome\Guest
     */
    private function new(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Welkome\Guest  $guest
     * @return \Illuminate\Http\Response
     */
    public function show(Guest $guest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Welkome\Guest  $guest
     * @return \Illuminate\Http\Response
     */
    public function edit(Guest $guest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Welkome\Guest  $guest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Guest $guest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Welkome\Guest  $guest
     * @return \Illuminate\Http\Response
     */
    public function destroy(Guest $guest)
    {
        //
    }

    /**
     * Display a listing of searched records.
     *
     * @param  \App\Welkome\Guest  $guest
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $guests = Guest::search(Input::get($request, 'query'))
            ->where('user_id', auth()->user()->parent)
            ->get(config('welkome.fields.guests'));

        if ($request->ajax()) {
            return response()->json([
                'guests' => $guests->toArray()
            ]);
        } else {
            # code...
        }
    }
}
