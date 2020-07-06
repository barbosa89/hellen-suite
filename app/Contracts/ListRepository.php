<?php

namespace App\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Standard repository
 */
interface ListRepository
{
    /**
     * Get paginated model collection
     *
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function all(int $perPage): LengthAwarePaginator;

    /**
     * Get search by query text
     *
     * @param string $id
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(string $query): LengthAwarePaginator;
}