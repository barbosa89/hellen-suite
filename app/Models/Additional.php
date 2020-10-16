<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Additional extends Model
{
    public function voucher()
    {
        return $this->belongsTo(\App\Models\Voucher::class);
    }
}
