<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;

class Additional extends Model
{
    public function voucher()
    {
        return $this->belongsTo(\App\Welkome\Voucher::class);
    }
}
