<?php

namespace App\Welkome;

use App\Traits\Queryable;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use Queryable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'business_name',
        'tin',
        'address',
        'phone',
        'mobile',
        'email',
        'status',
        'image',
        'created_at'
    ];

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
    protected $hidden = ['id', 'main_hotel', 'user_id'];

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

    public function vouchers()
    {
        return $this->hasMany(\App\Welkome\Voucher::class);
    }

    public function shifts()
    {
        return $this->hasMany(\App\Welkome\Shift::class);
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
        return $this->attributes['hash'] = (string) id_encode($this->attributes['id']);
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

    /**
     * Get the notes for the hotel.
     */
    public function notes()
    {
        return $this->hasMany(\App\Welkome\Note::class);
    }
}
