<?php

namespace App\Repositories;

use App\Contracts\GuestRepository as Repository;
use App\Models\Guest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class GuestRepository implements Repository
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return Guest::query()
            ->whereOwner()
            ->latest()
            ->filter($filters)
            ->paginate($perPage);
    }

    public function all(array $filters = []): Collection
    {
        return Guest::query()
            ->whereOwner()
            ->filter($filters)
            ->get();
    }

    public function find(int $id): Guest
    {
        return Guest::whereOwner()->where('id', $id);
    }

    public function create(array $data): Guest
    {
        $guest = new Guest;
        $guest->fill($data);
        $guest->user()->associate(id_parent());
        $guest->save();

        return $guest;
    }

    public function update(int $id, array $data): Guest
    {
        $guest = $this->find($id);
        $guest->fill($data);
        $guest->save();

        return $guest;
    }

    public function destroy(int $id): bool
    {
        $guest = $this->find($id);

        return $guest->delete();
    }

    public function search(string $query): LengthAwarePaginator
    {
        return Guest::whereOwner()
            ->whereLike(['name', 'last_name', 'dni', 'email'], $query)
            ->paginate(15, fields_get('guests'));

    }
}
