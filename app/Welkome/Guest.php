<?php

namespace App\Welkome;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Guest extends Model
{
    use Searchable;
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

    public $asYouType = true;    

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'dni' => $this->dni, 
            'name' => $this->name, 
            'last_name' => $this->last_name, 
            'email' => $this->email
        ];
    }

    public function children()
    {
        return $this->hasMany(\App\Welkome\Guest::class, 'responsible_adult');
    }

    public function parent()
    {
        return $this->belongsTo(\App\Welkome\Guest::class, 'responsible_adult');
    }

    public function invoices()
    {
        return $this->belongsToMany(\App\Welkome\Invoice::class);
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
}
