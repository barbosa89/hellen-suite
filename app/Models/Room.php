<?php

namespace App\Models;

use App\Traits\Queryable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Room extends Model
{
    use LogsActivity;
    use Queryable;

    public const OCCUPIED = '0';

    public const AVAILABLE = '1';

    public const CLEANING = '2';

    public const DISABLED = '3';

    public const MAINTENANCE = '4';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'number', 'description', 'price', 'min_price', 'status', 'is_suite', 'capacity', 'floor'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['hash', 'hotel_hash'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['id', 'hotel_id', 'user_id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'float',
        'min_price' => 'float',
        'capacity' => 'integer',
        'floor' => 'integer',
        'is_suite' => 'boolean',
        'tax' => 'float',
    ];

    /**
     * Hashing Room ID.
     *
     * @param  string  $value
     * @return void
     */
    public function getHashAttribute()
    {
        return $this->attributes['hash'] = (string) id_encode($this->attributes['id']);
    }

    /**
     * Hashing Hotel ID.
     *
     * @param  string  $value
     * @return void
     */
    public function getHotelHashAttribute()
    {
        return $this->attributes['hotel'] = (string) id_encode($this->attributes['hotel_id']);
    }

    public function vouchers()
    {
        return $this->belongsToMany(\App\Models\Voucher::class);
    }

    public function assets()
    {
        return $this->hasMany(\App\Models\Asset::class);
    }

    public function products()
    {
        return $this->belongsToMany(\App\Models\Product::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function guests()
    {
        return $this->belongsToMany(\App\Models\Guest::class);
    }

    public function hotel()
    {
        return $this->belongsTo(\App\Models\Hotel::class);
    }

    /**
     * Get all of the asset's maintenances.
     */
    public function maintenances()
    {
        return $this->morphMany(\App\Models\Maintenance::class, 'maintainable');
    }

    /**
     * Scope a query by owner.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOwner($query)
    {
        return $query->where('user_id', id_parent());
    }

    /**
     * Scope a query to select all columns.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSelectAll($query)
    {
        return $query->select(['id', 'user_id', 'hotel_id', ...$this->fillable]);
    }
}
