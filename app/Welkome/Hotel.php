<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    // The hotel owner
    public function owner()
    {
        return $this->belongsTo(\App\User::class);
    }

    // Employees assigned to one or more hotels
    public function employees()
    {
        return $this->belongsToMany(\App\User::class);
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
