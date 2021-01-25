<?php

namespace App\Contracts;

use App\Models\Voucher;
use Illuminate\Http\Response;

interface VoucherPrinter
{
    public function setVoucher(Voucher $document): self;

    public function buildDocument(): self;

    public function stream(): Response;
}
