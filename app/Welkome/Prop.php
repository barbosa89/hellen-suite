<?php

namespace App\Welkome;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Prop extends Model
{
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description', 'quantity', 'status', 'hotel_id', 'user_ud'
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
    protected $hidden = ['id'];

    public function hotel()
    {
        return $this->belongsTo(\App\Welkome\Hotel::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function vouchers()
    {
        return $this->belongsToMany(\App\Welkome\Voucher::class);
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
