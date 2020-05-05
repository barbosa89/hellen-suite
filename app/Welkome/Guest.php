<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Guest extends Model
{
    use LogsActivity;



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dni', 'name', 'last_name', 'gender', 'birthdate', 'responsible_adult',
        'identification_type_id', 'user_id', 'status', 'created_at'
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

    public function children()
    {
        return $this->hasMany(\App\Welkome\Guest::class, 'responsible_adult');
    }

    public function parent()
    {
        return $this->belongsTo(\App\Welkome\Guest::class, 'responsible_adult');
    }

    public function vouchers()
    {
        return $this->belongsToMany(\App\Welkome\Voucher::class);
    }

    public function identificationType()
    {
        return $this->belongsTo(\App\Welkome\IdentificationType::class, 'identification_type_id');
    }

    public function vehicles()
    {
        return $this->belongsToMany(\App\Welkome\Vehicle::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function rooms()
    {
        return $this->belongsToMany(\App\Welkome\Room::class);
    }

    public function country()
    {
        return $this->belongsTo(\App\Welkome\Country::class);
    }

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
}
