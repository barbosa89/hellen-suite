<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;

class Additional extends Model
{
    public function invoice()
    {
        return $this->belongsTo(\App\Welkome\Invoice::class);
    }
}
