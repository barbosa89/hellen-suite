<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    public function invoices()
    {
        return $this->belongsToMany(Welkome\Invoice::class);
    }

    public function assets()
    {
        return $this->belongsToMany(Welkome\Asset::class);
    }
}
