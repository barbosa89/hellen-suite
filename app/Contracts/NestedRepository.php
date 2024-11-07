<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Standard repository for nested resources
 */
interface NestedRepository
{
    /**
     * Get paginated model collection
     */
    public function paginate(int $parent, int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /**
     * Get complete model collection
     */
    public function all(int $parent, array $filters = []): Collection;

    /**
     * Get model
     */
    public function find(int $id): Model;

    /**
     * Create a new model
     */
    public function create(int $parent, array $data): Model;

    /**
     * Update model
     */
    public function update(int $id, array $data): Model;

    /**
     * Destroy model
     */
    public function destroy(int $id): bool;
}
