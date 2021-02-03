<?php

namespace App\Contracts;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface VoucherRepository extends NestedRepository
{
    /**
     * @param string $query
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(string $query): LengthAwarePaginator;

    /**
     * Get model collection
     *
     * @return \Illuminate\Support\Collection
     */
    public function list(): Collection;

    /**
     * Get model
     *
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function first(int $id): Model;
}
