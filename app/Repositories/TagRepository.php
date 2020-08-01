<?php

namespace App\Repositories;

use App\Repositories\Repository;
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
     * @param array $data
     * @return \App\Welkome\Tag
     */
    public function create(array $data): Tag
    {
        $tag = new Tag();
        $tag->description = $data['tag'];
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
     * @param int $id
     * @param array $data
     * @return \App\Welkome\Tag
     */
    public function update(int $id, array $data): Tag
    {
        $tag = $this->get($id);
        $tag->description = $data['description'];
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
     * @return \Illuminate\Support\Collection
     */
    public function all(): Collection
    {
        return Tag::whereUserId(id_parent())
            ->orderBy('description')
            ->get(['id', 'description', 'slug', 'user_id']);
    }

    /**
     * Tag search by query text
     *
     * @param string $query
     * @return \Illuminate\Support\Collection
     */
    public function search(string $query): Collection
    {
        return Tag::whereUserId(id_parent())
            ->whereLike(['description'], $query)
            ->get(['id', 'description', 'slug', 'user_id']);
    }
}
