<?php

namespace App\Welkome;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['hash'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['id'];

    // The hotel owner
    public function owner()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
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

    public function getHashAttribute()
    {
        return $this->attributes['hash'] = (string) Hashids::encode($this->attributes['id']);
    }

    public function products()
    {
        return $this->hasMany(\App\Welkome\Product::class);
    }

    public function services()
    {
        return $this->hasMany(\App\Welkome\Service::class);
    }

    public function assets()
    {
        return $this->hasMany(\App\Welkome\Asset::class);
    }

    public function props()
    {
        return $this->hasMany(\App\Welkome\Prop::class);
    }
}
