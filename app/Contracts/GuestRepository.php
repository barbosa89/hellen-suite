<?php

namespace App\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface GuestRepository extends Repository
{
    /**
     * @param string $query
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(string $query): LengthAwarePaginator;
}
