<?php

namespace App\Repositories;

use App\Models\Note;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Pure Eloquent Repository
 */
class NoteRepository implements Repository
{
    /**
     * Store new model
     *
     * @param array data
     */
    public function create(array $data): Note
    {
        $note = new Note;
        $note->fill($data);
        $note->hotel()->associate($data['hotel_id']);
        $note->user()->associate(id_parent());
        $note->saveOrFail();

        // Sync note tags
        $note->tags()->sync($data['tags']);

        return $note;
    }

    /**
     * Retrieve model by ID
     */
    public function get(int $id): Note
    {
        $note = Note::whereUserId(id_parent())
            ->whereId($id)
            ->first(Note::getColumnNames());

        return $note;
    }

    /**
     * Update model
     */
    public function update(int $id, array $data): Note
    {
        $note = $this->get($id);
        $note->content = $data['content'];
        $note->saveOrFail();

        return $note;
    }

    /**
     * Destroy model
     */
    public function destroy(int $id): bool
    {
        $note = $this->get($id);

        return $note->delete();
    }

    /**
     * Return a paginated Note collection
     */
    public function search(int $hotel, string $start, string $end, ?string $text = null): LengthAwarePaginator
    {
        return $this->filter($hotel, $start, $end, $text)
            ->paginate(
                config('settings.paginate'),
                Note::getColumnNames(['user_id', 'hotel_id'])
            );
    }

    /**
     * Return a Note collection
     */
    public function list(int $hotel, string $start, string $end, ?string $text = null): Collection
    {
        return $this->filter($hotel, $start, $end, $text)
            ->get(Note::getColumnNames(['user_id', 'hotel_id']));
    }

    /**
     * Prepare query by parameters
     */
    public function filter(int $hotel, string $start, string $end, ?string $text = null): Builder
    {
        return Note::query()
            ->whereUserId(id_parent())
            ->whereHotelId($hotel)
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end)
            ->when(! empty($text), function ($query) use ($text): void {
                $query->whereLike(['content'], $text);
            })
            ->orderBy('created_at', 'DESC')
            ->with([
                'tags' => function ($query): void {
                    $query->select(['id', 'slug']);
                },
            ]);
    }
}
