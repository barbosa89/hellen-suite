<?php

namespace App\Models;

use App\Traits\ColumnList;
use App\Traits\Queryable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Room extends Model
{
    use LogsActivity;
    use Queryable;

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
     * Scope a query to query by Id.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeById($query, int $id)
    {
        return $query->where('user_id', id_parent())
            ->where('id', $id);
    }
}
