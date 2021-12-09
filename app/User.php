<?php

namespace App;

use App\Constants\Roles;
use App\Traits\Hashable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasRoles;
    use Hashable;
    use Notifiable;
    use LogsActivity;
    use HasApiTokens;

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'status', 'email_verified_at'
    ];

    /**
     * @var array
     */
    protected $hidden = ['id', 'password', 'remember_token'];

    /**
     * @var array
     */
    protected $appends = ['hash'];

    /**
     * @var array
     */
    protected $casts = [
        'parent' => 'integer',
        'email_verified_at' => 'datetime',
    ];

    public function plans()
    {
        return $this->belongsToMany(\App\Models\Plan::class)
            ->withPivot('ends_at')
            ->withTimestamps();
    }

    public function invoices()
    {
        return $this->hasMany(\App\Models\Invoice::class);
    }

    public function hotels()
    {
        return $this->hasMany(\App\Models\Hotel::class);
    }

    public function headquarters()
    {
        return $this->belongsToMany(\App\Models\Hotel::class);
    }

    public function shifts()
    {
        return $this->hasMany(\App\Models\Shift::class);
    }

    public function vouchers()
    {
        return $this->hasMany(\App\Models\Voucher::class);
    }

    public function employees()
    {
        return $this->hasMany(User::class, 'parent');
    }

    public function boss()
    {
        return $this->belongsTo(User::class, 'parent');
    }

    public function guests()
    {
        return $this->hasMany(\App\Models\Guest::class);
    }

    public function services()
    {
        return $this->hasMany(\App\Models\Service::class);
    }

    public function rooms()
    {
        return $this->hasMany(\App\Models\Room::class);
    }

    public function vehicles()
    {
        return $this->hasMany(\App\Models\Vehicle::class);
    }

    public function assets()
    {
        return $this->hasMany(\App\Models\Asset::class);
    }

    public function products()
    {
        return $this->hasMany(\App\Models\Product::class);
    }

    public function companies()
    {
        return $this->hasMany(\App\Models\Company::class);
    }

    public function props()
    {
        return $this->hasMany(\App\Models\Prop::class);
    }

    /**
     * Get the notes for the user.
     */
    public function notes()
    {
        return $this->hasMany(\App\Models\Note::class);
    }

    /**
     * Get the tags for the user.
     */
    public function tags()
    {
        return $this->hasMany(\App\Models\Tag::class);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeOwner(Builder $query): Builder
    {
        return $query->where('parent', null)
            ->role(Roles::MANAGER);
    }
}
