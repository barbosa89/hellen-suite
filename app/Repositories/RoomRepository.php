<?php

namespace App\Repositories;

use App\Contracts\RoomRepository as Repository;
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
     * @param int $id
     * @throws Exception
     * @return \App\Models\Room
     */
    public function find(int $id): Room
    {
        return Room::findOrFail($id);
    }

    /**
     * @param integer $hotel
     * @param integer $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function paginate(int $hotel, int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return Room::where('user_id', id_parent())
            ->where('hotel_id', $hotel)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * @param integer $hotel
     * @param array $filters
     * @return Collection
     */
    public function all(int $hotel, array $filters = []): Collection
    {
        return Room::where('user_id', id_parent())
            ->where('hotel_id', $hotel)
            ->get();
    }

    /**
     * @param integer $hotel
     * @param array $data
     * @throws Exception
     * @return \App\Models\Room
     */
    public function create(int $hotel, array $data): Room
    {
        $room = new Room();
        $room->fill($data);
        $room->hotel()->associate($hotel);
        $room->user()->associate(id_parent());
        $room->saveOrFail();

        // Sync Room tags
        $room->tags()->sync($data['tags']);

        return $room;
    }

    /**
     * @param  integer $id
     * @param  array $data
     * @throws Exception
     * @return \App\Models\Room
     */
    public function update(int $id, array $data): Room
    {
        $room = $this->find($id);
        $room->fill($data);
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
        $room = $this->find($id);

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
                config('settings.paginate'),
                Room::getColumnNames(['user_id', 'hotel_id'])
            );
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
