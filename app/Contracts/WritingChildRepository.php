<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;

/**
 * Writing repository
 */
interface WritingChildRepository
{
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