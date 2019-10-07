<?php

namespace App;

use Zizaco\Entrust\Traits\EntrustUserTrait;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use EntrustUserTrait;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function hotels()
    {
        return $this->hasMany(Welkome\Hotel::class);
    }

    public function headquarters()
    {
        return $this->belongsToMany(Welkome\Hotel::class);
    }

    public function shifts()
    {
        return $this->hasMany(Welkome\Shift::class);
    }

    public function invoices()
    {
        return $this->hasMany(Welkome\Invoice::class);
    }

    public function employees()
    {
        return $this->hasMany(User::class, 'parent');
    }

    public function boss()
    {
        return $this->belongsTo(User::class, 'parent');
    }

    #####################################

    public function guests()
    {
        return $this->hasMany(Welkome\Guest::class);
    }

    public function services()
    {
        return $this->hasMany(Welkome\Service::class);
    }

    public function rooms()
    {
        return $this->hasMany(Welkome\Room::class);
    }

    public function vehicles()
    {
        return $this->hasMany(Welkome\Vehicle::class);
    }

    public function assets()
    {
        return $this->hasMany(Welkome\Asset::class);
    }

    public function products()
    {
        return $this->hasMany(Welkome\Product::class);
    }

    public function companies()
    {
        return $this->hasMany(Welkome\Company::class);
    }

    // public function matchRole(string $role = '')
    // {
    //     $this->whereHas('roles', function ($query) use ($role)
    //     {
    //         $query->where('name', $role);
    //     })->first(['id']);
    // }
}
