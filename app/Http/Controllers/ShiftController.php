<?php

namespace App\Http\Controllers;

use App\Welkome\Shift;
use App\Helpers\{Fields, Id};

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shifts = Shift::where('team_member', auth()->user()->id)
            ->whereHas('user', function ($query)
            {
                $query->where('id', Id::parent());
            })->with([
                'hotel' => function ($query)
                {
                    $query->select(Fields::get('hotels'));
                }
            ])->get(Fields::get('shifts'));

        return view('app.shifts.index', compact('shifts'));
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id)
    {
        $shift = Shift::where('team_member', auth()->user()->id)
            ->where('id', Id::get($id))
            ->with([
                'vouchers' => function($query)
                {
                    $query->select(Fields::get('vouchers'));
                },
                'vouchers.payments' => function($query)
                {
                    $query->select(Fields::get('payments'));
                },
                'hotel' => function($query)
                {
                    $query->select(Fields::get('hotels'));
                },
                'hotel.rooms' => function($query)
                {
                    $query->select(Fields::get('rooms'));
                }
            ])->first(Fields::get('shifts'));

        // List rooms with vouchers of the shift

        return view('app.shifts.show', compact('shift'));
    }
}
