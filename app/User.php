<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use EntrustUserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'status', 
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function entities()
    {
        return $this->hasMany(Welkome\Entity::class);
    }

    public function shifts()
    {
        return $this->hasMany(Welkome\Shift::class);
    }

    public function invoices()
    {
        return $this->hasMany(Welkome\Invoice::class);
    }

    public function children()
    {
        return $this->hasMany(User::class, 'parent');
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent');
    }
}
