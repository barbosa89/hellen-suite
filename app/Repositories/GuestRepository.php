<?php

namespace App\Repositories;

use App\Models\Guest;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Contracts\GuestRepository as Repository;

class GuestRepository implements Repository
{
    /**
     * @param int $perPage
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return Guest::query()
            ->whereOwner()
            ->latest()
            ->filter($filters)
            ->paginate($perPage);
    }

    /**
     * @param array $filters
     * @return \Illuminate\Support\Collection
     */
    public function all(array $filters = []): Collection
    {
        return Guest::query()
            ->whereOwner()
            ->filter($filters)
            ->get();
    }

    /**
     * @param integer $id
     * @return \App\Models\Guest
     */
    public function find(int $id): Guest
    {
        return Guest::whereOwner()->where('id', $id);
    }

    public function create(array $data): Guest
    {
        $guest = new Guest();
        $guest->fill($data);
        $guest->user()->associate(id_parent());
        $guest->save();

        return $guest;
    }

    /**
     * @param integer $id
     * @param array $data
     * @return \App\Models\Guest
     */
    public function update(int $id, array $data): Guest
    {
        $guest = $this->find($id);
        $guest->fill($data);
        $guest->save();

        return $guest;
    }

    /**
     * @param integer $id
     * @return boolean
     */
    public function destroy(int $id): bool
    {
        $guest = $this->find($id);

        return $guest->delete();
    }

    /**
     * @param string $query
     * @return LengthAwarePaginator
     */
    public function search(string $query): LengthAwarePaginator
    {
        return Guest::whereOwner()
            ->whereLike(['name', 'last_name', 'dni', 'email'], $query)
            ->paginate(15 , fields_get('guests'));

    }
}
