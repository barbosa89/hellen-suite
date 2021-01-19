<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface RoomRepository extends NestedRepository
{
    /**
     * @param string $query
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(string $query): LengthAwarePaginator;


    /**
     * Change model status
     *
     * @param integer $id
     * @param string $status
     * @return Model
     */
    public function toggle(int $id, string $status): Model;
}
