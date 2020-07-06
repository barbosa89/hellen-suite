<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;

/**
 * Standard repository
 */
interface BaseRepository
{
    /**
     * Create a new model
     *
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data): Model;

    /**
     * Get model
     *
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function get(int $id): Model;

    /**
     * Update model
     *
     * @param array $data
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update(array $data, int $id): Model;

    /**
     * Destroy model
     *
     * @param int $id
     * @return bool
     */
    public function destroy(int $id): bool;
}