<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Reading repository
 */
interface ReadingRepository
{
    /**
     * Get model
     *
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function findById(int $id): Model;

    /**
     * Get paginated model collection
     *
     * @param int $perPage
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /**
     * Get complete model collection
     *
     * @param array $filters
     * @return \Illuminate\Support\Collection
     */
    public function all(array $filters = []): Collection;
}