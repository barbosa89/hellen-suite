<?php

namespace App\Repository;

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
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Database\Eloquent\Model
     */
    public function create(Request $request): Model;

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
     * @param Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update(Request $request, int $id): Model;

    /**
     * Destroy model
     *
     * @param int $id
     * @return bool
     */
    public function destroy(int $id): bool;
}