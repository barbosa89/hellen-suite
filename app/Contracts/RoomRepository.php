<?php

namespace App\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface RoomRepository extends NestedRepository
{
    /**
     * @param string $query
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(string $query): LengthAwarePaginator;
}
