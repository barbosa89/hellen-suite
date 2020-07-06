<?php

namespace App\Repositories;

use App\Contracts\BaseRepository;
use App\Welkome\Note;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Pure Eloquent Repository
 */
class NoteRepository implements BaseRepository
{
    /**
     * Store new model
     *
     * @param array data
     * @return \App\Welkome\Note
     */
    public function create(array $data): Note
    {
        $note = new Note();
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
     *
     * @param  integer $id
     * @return \App\Welkome\Note
     */
    public function get(int $id): Note
    {
        $note = Note::whereUserId(id_parent())
            ->whereId($id)
            ->get(Note::getColumnNames());

        return $note;
    }

    /**
     * Update model
     *
     * @param  array $data
     * @param  integer $id
     * @return \App\Welkome\Note
     */
    public function update(array $data, int $id): Note
    {
        $note = $this->get($id);
        $note->content = $data['content'];
        $note->saveOrFail();

        return $note;
    }

    /**
     * Destroy model
     *
     * @param integer $id
     * @return boolean
     */
    public function destroy(int $id): bool
    {
        $note = $this->get($id);

        return $note->delete();
    }

    /**
     * Return a paginated Note collection
     *
     * @param integer $hotel
     * @param string $start
     * @param string $end
     * @param string $text
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(int $hotel, string $start, string $end, string $text = null): LengthAwarePaginator
    {
        return $this->filter($hotel, $start, $end, $text)
            ->paginate(
                config('welkome.paginate'),
                Note::getColumnNames(['user_id', 'hotel_id'])
            );
    }

    /**
     * Return a Note collection
     *
     * @param integer $hotel
     * @param string $start
     * @param string $end
     * @param string $text
     * @return \Illuminate\Support\Collection
     */
    public function list(int $hotel, string $start, string $end, string $text = null): Collection
    {
        return $this->filter($hotel, $start, $end, $text)
            ->get(Note::getColumnNames(['user_id', 'hotel_id']));
    }

    /**
     * Prepare query by parameters
     *
     * @param integer $hotel
     * @param string $start
     * @param string $end
     * @param string $text
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function filter(int $hotel, string $start, string $end, string $text = null): Builder
    {
        return Note::query()
            ->whereUserId(id_parent())
            ->whereHotelId($hotel)
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end)
            ->when(!empty($text), function ($query) use ($text) {
                $query->whereLike(['content'], $text);
            })
            ->orderBy('created_at', 'DESC')
            ->with([
                'tags' => function ($query)
                {
                    $query->select(['id', 'slug']);
                }
            ]);
    }
}
