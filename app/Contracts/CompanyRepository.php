<?php

namespace App\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface CompanyRepository extends Repository
{
    /**
     * @param string $query
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function search(string $query): LengthAwarePaginator;
}
