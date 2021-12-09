<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTag;
use App\Http\Requests\UpdateTag;
use App\Repositories\TagRepository;
use App\Models\Hotel;
use App\Models\Note;
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
     * @param \App\Repositories\TagRepository $tag
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
        $tags = $this->tag->all();

        if (request()->ajax()) {
            return response()->json($tags);
        }

        return view('app.tags.index', compact('tags'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTag $request)
    {
        $tag = $this->tag->create($request->validated());

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

        $hotel = Hotel::findHash($hotel, fields_get('hotels'));

        $notes = Note::whereTag($hotel, $tag)
            ->paginate(
                config('settings.paginate'),
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
        $tag = $this->tag->get(id_decode($id));

        return view('app.tags.edit', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTag $request, string $id)
    {
        $this->tag->update(id_decode($id), $request->validated());

        flash(trans('common.updatedSuccessfully'))->success();

        return redirect()->route('tags.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id)
    {
        if ($this->tag->destroy(id_decode($id))) {
            if (request()->ajax()) {
                return response()->json([
                    'status' => true
                ]);
            }

            flash(trans('common.deletedSuccessfully'))->success();

            return redirect()->route('tags.index');
        }

        flash(trans('common.error'))->error();

        return redirect()->route('tags.index');
    }

    /**
     * Tag searching
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $query = clean_param($request->input('search'));

        return response()->json([
            'results' => $this->tag->search($query)
        ]);
    }
}
