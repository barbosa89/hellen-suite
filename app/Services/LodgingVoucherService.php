<?php

namespace App\Services;

use App\Repositories\VoucherRepository;
use App\Welkome\Voucher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use InvalidArgumentException;

class LodgingVoucherService extends VoucherRepository
{
    /**
     * Eloquent query builder
     *
     * @var \Illuminate\Database\Eloquent\Builder
     */
    private Builder $model;
}
