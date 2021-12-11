<?php

namespace App\Models;

use App\User;
use App\Models\Note;
use App\Models\Prop;
use App\Models\Room;
use App\Models\Asset;
use App\Models\Shift;
use App\Models\Product;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Voucher;
use App\Traits\Queryable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Hotel extends Model
{
    use Queryable;

    /**
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
     * @var array
     */
    protected $appends = ['hash'];

    /**
     * @var array
     */
    protected $hidden = ['id', 'main_hotel', 'user_id'];

    public function getHashAttribute()
    {
        return $this->attributes['hash'] = (string) id_encode($this->attributes['id']);
    }

    // The hotel owner
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class);
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function main(): BelongsTo
    {
        return $this->belongsTo(Hotel::class, 'main_hotel');
    }

    public function headquarters(): HasMany
    {
        return $this->hasMany(Hotel::class, 'main_hotel');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function props(): HasMany
    {
        return $this->hasMany(Prop::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function settings(): MorphMany
    {
        return $this->morphMany(Setting::class, 'configurable');
    }

    public function scopeAssigned(Builder $query): Builder
    {
        return $query->owner()
            ->where('status', true)
            ->when(auth()->user()->hasRole('receptionist'), function ($query)
            {
                $query->whereHas('employees', function ($query)
                {
                    $query->where('id', auth()->user()->id);
                });
            });
    }
}
