<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    public function invoices()
    {
        return $this->belongsToMany(Welkome\Invoice::class);
    }
}
