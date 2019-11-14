<?php

namespace App\Welkome;

use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Room extends Model
{
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'number', 'description', 'price', 'status', 'user_id', 'tax_included', 'is_suite', 'capacity', 'floors'
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

    public function invoices()
    {
        return $this->belongsToMany(\App\Welkome\Invoice::class);
    }

    public function assets()
    {
        return $this->hasMany(\App\Welkome\Asset::class);
    }

    public function products()
    {
        return $this->belongsToMany(\App\Welkome\Product::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function guests()
    {
        return $this->belongsToMany(\App\Welkome\Guest::class);
    }

    public function hotel()
    {
        return $this->belongsTo(\App\Welkome\Hotel::class);
    }

    /**
     * Get all of the asset's maintenances.
     */
    public function maintenances()
    {
        return $this->morphMany(\App\Welkome\Maintenance::class, 'maintainable');
    }

    /**
     * Hashing Room ID.
     *
     * @param  string  $value
     * @return void
     */
    public function getHashAttribute()
    {
        return $this->attributes['hash'] = (string) Hashids::encode($this->attributes['id']);
    }
}
