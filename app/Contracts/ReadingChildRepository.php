<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Reading repository
 */
interface ReadingChildRepository
{
    /**
     * Get model
     *
     * @param int $id
     * @param array $relationships
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function find(int $id, array $relationships = [], array $filters = []): Model;

    /**
     * Get paginated model collection
     *
     * @param int $parent
     * @param int $perPage
     * @param array $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginate(int $parent, int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /**
     * Get complete model collection
     *
     * @param int $parent
     * @param array $filters
     * @return \Illuminate\Support\Collection
     */
    public function all(int $parent, array $filters = []): Collection;
}
