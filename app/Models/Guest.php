<?php

namespace App\Models;

use App\Traits\Queryable;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Guest extends Model
{
    use Queryable;
    use LogsActivity;

    public const SCOPE_FILTERS = [
        'from_date',
        'status',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dni',
        'name',
        'last_name',
        'gender',
        'birthdate',
        'responsible_adult',
        'identification_type_id',
        'user_id',
        'status',
        'created_at',
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
     * Get the guest's full name.
     *
     * @param  string  $value
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->name} {$this->last_name}";
    }

    /**
     * Hashing ID.
     *
     * @param  string  $value
     * @return void
     */
    public function getHashAttribute()
    {
        return $this->attributes['hash'] = (string) id_encode($this->attributes['id']);
    }

    public function children()
    {
        return $this->hasMany(\App\Models\Guest::class, 'responsible_adult');
    }

    public function parent()
    {
        return $this->belongsTo(\App\Models\Guest::class, 'responsible_adult');
    }

    public function vouchers()
    {
        return $this->belongsToMany(\App\Models\Voucher::class);
    }

    public function identificationType()
    {
        return $this->belongsTo(\App\Models\IdentificationType::class, 'identification_type_id');
    }

    public function vehicles()
    {
        return $this->belongsToMany(\App\Models\Vehicle::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function rooms()
    {
        return $this->belongsToMany(\App\Models\Room::class);
    }

    public function country()
    {
        return $this->belongsTo(\App\Models\Country::class);
    }

    /**
     * Scope a query to guest(s) is staying in the hotel.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsStaying($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope a query to guest(s) is not staying in the hotel.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsNotStaying($query)
    {
        return $query->where('status', false);
    }

    /**
     * Scope a query to guest(s) by status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus($query, string $status)
    {
        if ($query->hasNamedScope($status)) {
            $query->{$status}();
        }

        return $query;
    }
}
