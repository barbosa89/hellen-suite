<?php

namespace App\Repository;

use App\Repository\Repository;
use App\Welkome\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Pure Eloquent Repository
 */
class TagRepository implements Repository
{
    /**
     * Create new Tag
     *
     * @param   \Illuminate\Http\Request $request
     * @return  \App\Welkome\Tag
     */
    public function create(Request $request): Tag
    {
        $tag = new Tag();
        $tag->description = $request->tag;
        $tag->user()->associate(id_parent());
        $tag->saveOrFail();

        return $tag;
    }

    /**
     * Retrieve model by ID
     *
     * @param  integer $id
     * @return \App\Welkome\Tag
     */
    public function get(int $id): Tag
    {
        $tag = Tag::whereUserId(id_parent())
            ->whereId($id)
            ->first(['id', 'description', 'slug', 'user_id']);

        abort_if(empty($tag), 404);

        return $tag;
    }

    /**
     * Update model
     *
     * @param  Illuminate\Http\Request $request
     * @param  integer $id
     * @return \App\Welkome\Tag
     */
    public function update(Request $request, int $id): Tag
    {
        $tag = $this->get($id);
        $tag->description = $request->description;
        $tag->saveOrFail();

        return $tag;
    }

    /**
     * Destroy model
     *
     * @param integer $id
     * @return boolean
     */
    public function destroy(int $id): bool
    {
        $tag = $this->get($id);

        return $tag->delete();
    }

    /**
     * Get all tags
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return Tag::whereUserId(id_parent())
            ->orderBy('description')
            ->get(['id', 'description', 'slug', 'user_id']);
    }
}
