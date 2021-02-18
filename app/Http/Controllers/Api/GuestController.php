<?php

namespace App\Http\Controllers\Api;

use App\Models\Guest;
use Illuminate\Http\Request;
use App\Contracts\GuestRepository;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;

class GuestController extends Controller
{
    public GuestRepository $guest;

    public function __construct(GuestRepository $guest)
    {
        $this->guest = $guest;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $validated = request()->validate([
            'from_date' => 'bail|nullable|date|before_or_equal:today',
            'status' => 'bail|nullable|string|in:is_staying,is_not_staying',
        ]);

        $guests = $this->guest->paginate(
            request()->get('per_page', 15),
            Arr::only($validated, Guest::SCOPE_FILTERS),
        );

        return response()->json([
            'guests' => $guests,
        ]);
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
