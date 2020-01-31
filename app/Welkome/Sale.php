<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    public function product()
    {
        return $this->belongsTo(\App\Welkome\Product::class);
    }
}
