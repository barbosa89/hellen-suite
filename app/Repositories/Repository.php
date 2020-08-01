<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Standard repository
 */
interface Repository
{
    /**
     * Create a new model
     *
     * @param array $data
     * @return Illuminate\Database\Eloquent\Model
     */
    public function create(array $data): Model;

    /**
     * Get model
     *
     * @param int $id
     * @return Illuminate\Database\Eloquent\Model
     */
    public function get(int $id): Model;

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