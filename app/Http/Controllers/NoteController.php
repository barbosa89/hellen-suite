<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNote;
use App\Welkome\Hotel;
use App\Welkome\Note;
use App\Welkome\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hotels = Hotel::assigned()->get(Hotel::getColumnNames());

        return view('app.notes.index', compact('hotels'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $hotels = Hotel::assigned()->get(Hotel::getColumnNames());
        $tags = Tag::whereUserId(id_parent())->get(['id', 'description', 'user_id']);

        return view('app.notes.create', compact('hotels', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreNote $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function show(Note $note)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function edit(Note $note)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Note $note)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Note  $note
     * @return \Illuminate\Http\Response
     */
    public function destroy(Note $note)
    {
        //
    }

    /**
     * Search notes between dates and hotel.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hotel' => 'required|string|hashed_exists:hotels,id',
            'start' => 'required|date',
            'end' => 'required|after:start'
        ]);

        if ($validator->fails()) {
            return redirect(url()->previous())
                        ->withErrors($validator)
                        ->withInput();
        }

        $start = param_clean($request->start);
        $end = param_clean($request->end);

        $hotel = Hotel::whereUserId(id_parent())
            ->whereId(id_decode($request->hotel))
            ->first(['id', 'business_name']);

        $notes = Note::whereUserId(id_parent())
            ->whereHotelId(id_decode($request->hotel))
            ->whereBetween('created_at', [$start, $end])
            ->with([
                'tags' => function ($query)
                {
                    $query->select(['id', 'slug']);
                }
            ])->paginate(
                config('welkome.paginate'),
                Note::getColumnNames(['user_id', 'hotel_id'])
            );

        return view('app.notes.search', compact('notes', 'start', 'end', 'hotel'));
    }
}
