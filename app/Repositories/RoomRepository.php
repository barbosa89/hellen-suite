<?php

namespace App\Repositories;

use Exception;
use App\Models\Room;
use Illuminate\Support\Collection;
use App\Contracts\RoomRepository as Repository;
use Illuminate\Pagination\LengthAwarePaginator;

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
        return Room::owner()
            ->where('id', $id)
            ->with([
                'hotel' => function ($query)
                {
                    $query->select(fields_get('hotels'));
                }
            ])
            ->firstOrFail(fields_get('rooms'));
    }

    /**
     * @param integer $hotel
     * @param integer $perPage
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function paginate(int $hotel, int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return Room::owner()
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
        return Room::owner()
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
        $room->status = Room::AVAILABLE;

        if ((int) $data['tax_status'] == 1) {
            $room->tax = (float) $data['tax'];
        }

        $room->hotel()->associate($hotel);
        $room->user()->associate(id_parent());
        $room->saveOrFail();

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


        if ((int) $data['tax_status'] == 1) {
            $room->tax = (float) $data['tax'];
        } else {
            $room->tax = 0.0;
        }

        $room->is_suite = (int) $data['is_suite'];

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
        $room = Room::owner()
            ->where('id', $id)
            ->doesntHave('vouchers')
            ->first(fields_get('rooms'));

        if (empty($room)) {
            return false;
        }

        return $room->delete();
    }

    /**
     * @param string $query
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(string $query): LengthAwarePaginator
    {
        return Room::owner()
            ->whereLike(['number', 'description'], $query)
            ->with([
                'hotel' => function ($query)
                {
                    $query->select(['id', 'business_name']);
                }
            ])
            ->paginate(config('settings.paginate'), fields_get('rooms'));
    }

    /**
     * Change Room status
     *
     * @param integer $id
     * @param string $status
     * @throws Exception
     * @return \App\Models\Room
     */
    public function toggle(int $id, string $status): Room
    {
        $room = Room::owner()
            ->where('id', $id)
            ->where('status', '!=', Room::OCCUPIED)
            ->firstOrFail(fields_get('rooms'));

        if ($status == Room::AVAILABLE and $room->canEnable()) {
            $room->status = Room::AVAILABLE;
        }

        if ($status == Room::DISABLED and $room->canDisable()) {
            $room->status = Room::DISABLED;
        }

        if ($status == Room::MAINTENANCE and $room->canDoMaintenance()) {
            $room->status = Room::MAINTENANCE;
        }

        $room->saveOrFail();

        return $room;
    }
}
