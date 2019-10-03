<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function invoices()
    {
        return $this->hasMany(\App\Welkome\Invoice::class);
    }

    public function rooms()
    {
        return $this->hasMany(\App\Welkome\Room::class);
    }

    public function main()
    {
        return $this->belongsTo(Hotel::class, 'main_hotel');
    }

    public function headquarters()
    {
        return $this->hasMany(Hotel::class, 'main_hotel');
    }
}
