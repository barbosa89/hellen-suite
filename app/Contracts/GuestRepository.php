<?php

namespace App\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface GuestRepository extends Repository
{
    public function search(string $query): LengthAwarePaginator;
}
