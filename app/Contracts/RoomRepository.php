<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface RoomRepository extends NestedRepository
{
    public function search(string $query): LengthAwarePaginator;

    /**
     * Change model status
     */
    public function toggle(int $id, string $status): Model;
}
