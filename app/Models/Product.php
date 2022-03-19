<?php

namespace App\Models;

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

    public function vouchers()
    {
        return $this->belongsToMany(\App\Models\Voucher::class);
    }

    public function rooms()
    {
        return $this->belongsToMany(\App\Models\Room::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function hotel()
    {
        return $this->belongsTo(\App\Models\Hotel::class);
    }

    /**
     * Hashing Product ID.
     *
     * @param  string  $value
     * @return void
     */
    public function getHashAttribute()
    {
        return $this->attributes['hash'] = (string) id_encode($this->attributes['id']);
    }
}
