<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public function invoice()
    {
        return $this->belongsTo(Welkome\Invoice::class);
    }
}
