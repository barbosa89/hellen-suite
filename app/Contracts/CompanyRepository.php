<?php

namespace App\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface CompanyRepository extends Repository
{
    public function search(string $query): LengthAwarePaginator;
}
