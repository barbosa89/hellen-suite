<?php

namespace App\Models;

use App\Traits\Queryable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hotel extends Model
{
    use Queryable;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'business_name',
        'tin',
        'address',
        'phone',
        'mobile',
        'email',
        'status',
        'image',
        'created_at'
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
    protected $hidden = ['id', 'main_hotel', 'user_id'];

    // The hotel owner
    public function owner()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    // Employees assigned to one or more hotels
    public function employees()
    {
        return $this->belongsToMany(\App\Models\User::class);
    }

    public function vouchers()
    {
        return $this->hasMany(\App\Models\Voucher::class);
    }

    public function shifts()
    {
        return $this->hasMany(\App\Models\Shift::class);
    }

    public function rooms()
    {
        return $this->hasMany(\App\Models\Room::class);
    }

    public function main()
    {
        return $this->belongsTo(Hotel::class, 'main_hotel');
    }

    public function headquarters()
    {
        return $this->hasMany(Hotel::class, 'main_hotel');
    }

    public function getHashAttribute()
    {
        return $this->attributes['hash'] = (string) id_encode($this->attributes['id']);
    }

    public function products()
    {
        return $this->hasMany(\App\Models\Product::class);
    }

    public function services()
    {
        return $this->hasMany(\App\Models\Service::class);
    }

    public function assets()
    {
        return $this->hasMany(\App\Models\Asset::class);
    }

    public function props()
    {
        return $this->hasMany(\App\Models\Prop::class);
    }

    /**
     * Get the notes for the hotel.
     */
    public function notes()
    {
        return $this->hasMany(\App\Models\Note::class);
    }

    /**
     * Scope a query to get assigned hotels by role.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAssigned($query)
    {
        return $query->whereUserId(id_parent())
            ->whereStatus(true)
            ->when(auth()->user()->hasRole('receptionist'), function ($query)
            {
                $query->whereHas('employees', function ($query)
                {
                    $query->where('id', auth()->user()->id);
                });
            });
    }

    /**
     * Scope a query to get a hotel by id.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeById($query, int $id)
    {
        return $query->whereUserId(id_parent())
            ->whereId($id)
            ->first(fields_get('hotels'));
    }
}
