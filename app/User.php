<?php

namespace App;

use Laravel\Scout\Searchable;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use EntrustUserTrait;
    use LogsActivity;
    use Searchable;

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

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $array = $this->toArray();

        return $array;
    }

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
