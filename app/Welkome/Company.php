<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Vinkla\Hashids\Facades\Hashids;

class Company extends Model
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
        return $this->hasMany(\App\Welkome\Invoice::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
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
