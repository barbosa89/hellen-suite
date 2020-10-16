<?php

namespace App\Repositories;

use App\Contracts\Repository;
use App\Models\Room;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Pure Eloquent Repository
 */
class RoomRepository implements Repository
{
    /**
     * Get model
     *
     * @param int $id
     * @return \App\Models\Room
     */
    public function findById(int $id): Room
    {
        $room = Room::byId($id)
            ->theseColumns()
            ->firstOrFail();

        // $room->load();

        return $room;
    }

    /**
     * Get paginated model collection
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return Room::where('user_id', id_parent())
            ->theseColumns()
            ->paginate($perPage);
    }

    /**
     * Get complete model collection
     *
     * @param int $perPage
     * @return \Illuminate\Support\Collection
     */
    public function all(array $filters = []): Collection
    {
        return Room::where('user_id', id_parent())
            ->theseColumns()
            ->get();
    }

    /**
     * Store new model
     *
     * @param array data
     * @return \App\Models\Room
     */
    public function create(array $data): Room
    {
        $room = new Room();
        $room->fill($data);
        $room->hotel()->associate($data['hotel_id']);
        $room->user()->associate(id_parent());
        $room->saveOrFail();

        // Sync Room tags
        $room->tags()->sync($data['tags']);

        return $room;
    }

    /**
     * Update model
     *
     * @param  integer $id
     * @param  array $data
     * @return \App\Models\Room
     */
    public function update(int $id, array $data): Room
    {
        $room = $this->findById($id);
        $room->content = $data['content'];
        $room->saveOrFail();

        return $room;
    }

    /**
     * Destroy model
     *
     * @param integer $id
     * @return boolean
     */
    public function destroy(int $id): bool
    {
        $room = $this->findById($id);

        return $room->delete();
    }

    /**
     * Return a paginated Room collection
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
                Room::getColumnNames(['user_id', 'hotel_id'])
            );
    }

    /**
     * Return a Room collection
     *
     * @param integer $hotel
     * @param string $start
     * @param string $end
     * @param string $text
     * @return \Illuminate\Support\Collection
     */
    // public function list(int $hotel, string $start, string $end, string $text = null): Collection
    // {
    //     return $this->filter($hotel, $start, $end, $text)
    //         ->get(Room::getColumnNames(['user_id', 'hotel_id']));
    // }

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
        return Room::query()
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
