<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTag;
use App\Repository\TagRepository;
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

        return response()->json([
            'status' => $tag instanceof Tag ? true : false
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function show(Tag $tag)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function edit(Tag $tag)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tag $tag)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tag $tag)
    {
        //
    }
}
