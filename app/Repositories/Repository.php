<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

/**
 * Standard repository
 */
interface Repository
{
    /**
     * Create a new model
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function create(array $data): Model;

    /**
     * Get model
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function get(int $id): Model;

    /**
     * Update model
     */
    public function update(int $id, array $data): Model;

    /**
     * Destroy model
     */
    public function destroy(int $id): bool;
}
