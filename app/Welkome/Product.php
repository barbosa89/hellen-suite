<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function invoices()
    {
        return $this->belongsToMany(Welkome\Invoice::class);
    }

    public function rooms()
    {
        return $this->belongsToMany(Welkome\Room::class);
    }
}
