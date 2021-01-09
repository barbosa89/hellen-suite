<?php

namespace App\Contracts;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Standard repository for nested resources
 */
interface NestedRepository
{
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

    /**
     * Get model
     *
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function find(int $id): Model;

    /**
     * Create a new model
     *
     * @param int $parent
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(int $parent, array $data): Model;

    /**
     * Update model
     *
     * @param int $id
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update(int $id, array $data): Model;

    /**
     * Destroy model
     *
     * @param int $id
     * @return bool
     */
    public function destroy(int $id): bool;
}
