<?php

namespace App\Welkome;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{
    use LogsActivity;

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

    public function invoices()
    {
        return $this->belongsToMany(\App\Welkome\Invoice::class);
    }

    public function rooms()
    {
        return $this->belongsToMany(\App\Welkome\Room::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function hotel()
    {
        return $this->belongsTo(\App\Welkome\Hotel::class);
    }

    public function sales()
    {
        return $this->hasMany(\App\Welkome\Sale::class);
    }

    /**
     * Hashing Product ID.
     *
     * @param  string  $value
     * @return void
     */
    public function getHashAttribute()
    {
        return $this->attributes['hash'] = (string) Hashids::encode($this->attributes['id']);
    }
}
