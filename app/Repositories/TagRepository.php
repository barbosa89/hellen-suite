<?php

namespace App\Repositories;

use App\Models\Tag;
use Illuminate\Support\Collection;

/**
 * Pure Eloquent Repository
 */
class TagRepository implements Repository
{
    /**
     * Create new Tag
     */
    public function create(array $data): Tag
    {
        $tag = new Tag;
        $tag->description = $data['tag'];
        $tag->user()->associate(id_parent());
        $tag->saveOrFail();

        return $tag;
    }

    /**
     * Retrieve model by ID
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
     */
    public function destroy(int $id): bool
    {
        $tag = $this->get($id);

        return $tag->delete();
    }

    /**
     * Get all tags
     */
    public function all(): Collection
    {
        return Tag::whereUserId(id_parent())
            ->orderBy('description')
            ->get(['id', 'description', 'slug', 'user_id']);
    }

    /**
     * Tag search by query text
     */
    public function search(string $query): Collection
    {
        return Tag::whereUserId(id_parent())
            ->whereLike(['description'], $query)
            ->get(['id', 'description', 'slug', 'user_id']);
    }
}
