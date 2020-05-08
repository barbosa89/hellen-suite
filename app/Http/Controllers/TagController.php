<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTag;
use App\Repository\TagRepository;
use App\Welkome\Hotel;
use App\Welkome\Note;
use App\welkome\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Tag repository Eloquent based
     *
     * @var TagRepository
     */
    public TagRepository $tag;

    /**
     * Construct function
     *
     * @param \App\Repository\TagRepository $tag
     */
    public function __construct(TagRepository $tag)
    {
        $this->tag = $tag;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tags = Tag::whereUserId(id_parent())
            ->paginate(
                config('welkome.paginate'),
                ['id', 'description', 'slug', 'user_id']
            );

        return response()->json($tags);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTag $request)
    {
        $tag = $this->tag->create($request);

        return response()->json($tag);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @param  string  $hotel
     * @return \Illuminate\Http\Response
     */
    public function show(string $id, string $hotel)
    {
        $tag = $this->tag->get(id_decode($id));

        // TODO: Cargar por repositorio
        $hotel = Hotel::whereUserId(id_parent())
            ->whereId(id_decode($hotel))
            ->first(['id', 'business_name']);

        $notes = Note::whereUserId(id_parent())
            ->whereHotelId($hotel->id)
            ->whereHas('tags', function ($query) use ($tag)
            {
                $query->where('id', $tag->id);
            })->paginate(
                config('welkome.paginate'),
                Note::getColumnNames()
            );

        return view('app.tags.show', compact('tag', 'notes', 'hotel'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        //
    }
}
