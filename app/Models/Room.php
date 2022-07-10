<?php

namespace App\Models;

use App\Traits\Queryable;
use App\Traits\InteractWithLogs;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{
    use Queryable;
    use HasFactory;
    use LogsActivity;
    use InteractWithLogs;

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

    public function getHashAttribute(): string
    {
        return $this->attributes['hash'] = (string) id_encode($this->attributes['id']);
    }

    public function getHotelHashAttribute(): string
    {
        return $this->attributes['hotel_hash'] = id_encode($this->attributes['hotel_id']);
    }

    /**
     * @return string
     */
    public function getAvailableAttribute(): string
    {
        return self::AVAILABLE;
    }

    /**
     * @return string
     */
    public function getDisabledAttribute(): string
    {
        return self::DISABLED;
    }

    /**
     * @return string
     */
    public function getMaintenanceAttribute(): string
    {
        return self::MAINTENANCE;
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
        return $this->belongsTo(\App\Models\User::class);
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
     * Scope a query to select all columns.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSelectAll($query)
    {
        return $query->select(['id', 'user_id', 'hotel_id', ...$this->fillable]);
    }

    /**
     * @return boolean
     */
    public function canDisable(): bool
    {
        return in_array($this->status, [self::AVAILABLE, self::CLEANING, self::MAINTENANCE]);
    }

    /**
     * @return boolean
     */
    public function canEnable(): bool
    {
        return in_array($this->status, [self::CLEANING, self::DISABLED, self::MAINTENANCE]);
    }

    /**
     * @return boolean
     */
    public function canDoMaintenance(): bool
    {
        return in_array($this->status, [self::AVAILABLE, self::CLEANING, self::DISABLED]);
    }

    /**
     * @return boolean
     */
    public function isToggleable(): bool
    {
        return $this->status != self::OCCUPIED;
    }

    /**
     * @return boolean
     */
    public function isFree(): bool
    {
        return $this->status == self::AVAILABLE;
    }
}
