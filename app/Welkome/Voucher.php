<?php

namespace App\Welkome;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Voucher extends Model
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

    public function rooms()
    {
        return $this->belongsToMany(\App\Welkome\Room::class);
    }

    public function payments()
    {
        return $this->hasMany(\App\Welkome\Payment::class);
    }

    public function products()
    {
        return $this->belongsToMany(\App\Welkome\Product::class);
    }

    public function services()
    {
        return $this->belongsToMany(\App\Welkome\Service::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function company()
    {
        return $this->belongsTo(\App\Welkome\Company::class);
    }

    public function guests()
    {
        return $this->belongsToMany(\App\Welkome\Guest::class);
    }

    public function hotel()
    {
        return $this->belongsTo(\App\Welkome\Hotel::class);
    }

    public function additionals()
    {
        return $this->hasMany(\App\Welkome\Additional::class);
    }

    public function shifts()
    {
        return $this->belongsToMany(\App\Welkome\Shift::class);
    }

    /**
     * Hashing ID.
     *
     * @param  string  $value
     * @return void
     */
    public function getHashAttribute()
    {
        return $this->attributes['hash'] = (string) Hashids::encode($this->attributes['id']);
    }
}
