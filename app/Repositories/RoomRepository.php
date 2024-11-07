<?php

namespace App\Repositories;

use App\Contracts\RoomRepository as Repository;
use App\Models\Room;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Pure Eloquent Repository
 */
class RoomRepository implements Repository
{
    /**
     * @throws Exception
     */
    public function find(int $id): Room
    {
        return Room::whereOwner()
            ->where('id', $id)
            ->with([
                'hotel' => function ($query): void {
                    $query->select(fields_get('hotels'));
                },
            ])
            ->firstOrFail(fields_get('rooms'));
    }

    public function paginate(int $hotel, int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return Room::whereOwner()
            ->where('hotel_id', $hotel)
            ->latest()
            ->paginate($perPage);
    }

    public function all(int $hotel, array $filters = []): Collection
    {
        return Room::whereOwner()
            ->where('hotel_id', $hotel)
            ->get();
    }

    /**
     * @throws Exception
     */
    public function create(int $hotel, array $data): Room
    {
        $room = new Room;
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
     * @throws Exception
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
     */
    public function destroy(int $id): bool
    {
        $room = Room::whereOwner()
            ->where('id', $id)
            ->doesntHave('vouchers')
            ->first(fields_get('rooms'));

        if (empty($room)) {
            return false;
        }

        return $room->delete();
    }

    public function search(string $query): LengthAwarePaginator
    {
        return Room::whereOwner()
            ->whereLike(['number', 'description'], $query)
            ->with([
                'hotel' => function ($query): void {
                    $query->select(['id', 'business_name']);
                },
            ])
            ->paginate(config('settings.paginate'), fields_get('rooms'));
    }

    /**
     * Change Room status
     *
     * @throws Exception
     */
    public function toggle(int $id, string $status): Room
    {
        $room = Room::whereOwner()
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
