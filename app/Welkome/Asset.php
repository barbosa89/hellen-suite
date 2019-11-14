<?php

namespace App\Welkome;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Asset extends Model
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

    use LogsActivity;

    public function room()
    {
        return $this->belongsTo(\App\Welkome\Room::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function hotel()
    {
        return $this->belongsTo(\App\Welkome\Hotel::class);
    }

    /**
     * Get all of the asset's transactions.
     */
    public function transactions()
    {
        return $this->morphMany(\App\Welkome\Transaction::class, 'transactionable');
    }

    /**
     * Get all of the asset's maintenances.
     */
    public function maintenances()
    {
        return $this->morphMany(\App\Welkome\Maintenance::class, 'maintainable');
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
