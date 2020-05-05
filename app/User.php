<?php

namespace App;

use Spatie\Activitylog\Traits\LogsActivity;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
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

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function hotels()
    {
        return $this->hasMany(Welkome\Hotel::class);
    }

    public function configurations()
    {
        return $this->belongsToMany(Welkome\Configuration::class);
    }

    public function headquarters()
    {
        return $this->belongsToMany(Welkome\Hotel::class);
    }

    public function shifts()
    {
        return $this->hasMany(Welkome\Shift::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Welkome\Voucher::class);
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

    public function props()
    {
        return $this->hasMany(Welkome\Prop::class);
    }

    /**
     * Get the tags for the user.
     */
    public function tags()
    {
        return $this->hasMany(\App\Welkome\Tag::class);
    }

	/**
     * Unhash an ID collection.
     *
     * @param  array	$ids
     * @return array
     */
    public function getAllPermissionsAttribute() {
        $permissions = [];

        $user = $this->where('id', auth()->user()->id)
            ->with([
                'permissions' => function ($query)
                {
                    $query->select(['id', 'name', 'guard_name']);
                }
            ])->first(['id']);

        $user->permissions->each(function ($permission) use (&$permissions)
        {
            $permissions[] = $permission->name;
        });

        return $permissions;
    }
}
