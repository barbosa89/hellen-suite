<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

interface VoucherRepository extends NestedRepository
{
    public function search(string $query): LengthAwarePaginator;

    /**
     * Get model collection
     */
    public function list(): Collection;

    /**
     * Get model
     */
    public function first(int $id): Model;

    public function queryGuestChecks(int $hotelId, Carbon $startDate, Carbon $endDate): Collection;
}
