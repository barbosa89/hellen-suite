<?php

namespace App;

use App\Constants\Roles;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, LogsActivity, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'status', 'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'parent' => 'integer',
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

    public function configurations()
    {
        return $this->belongsToMany(\App\Models\Configuration::class);
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
