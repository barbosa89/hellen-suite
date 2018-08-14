<?php

namespace App\Welkome;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Invoice extends Model
{
    use Searchable;
    use LogsActivity;

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    // public function toSearchableArray()
    // {
    //     // $company = empty($this->company) ? null : $this->company->name;
    //     // $tin = empty($this->company) ? null : $this->company->tin;
    //     // $names = empty($this->guests) ? null : $this->guests->implode('name', ', ');
    //     // $last_names = empty($this->guests) ? null : $this->guests->implode('last_name', ', ');
    //     // $dni = empty($this->guests) ? null : $this->guests->implode('dni', ', ');
    //     // $emails = empty($this->guests) ? null : $this->guests->implode('email', ', ');

    //     $array = $this->toArray();

    //     return $array;
    // }

    public function rooms()
    {
        return $this->belongsToMany(\App\Welkome\Room::class);
    }

    public function payments()
    {
        return $this->hasMany(\App\Welkome\Payment::class);
    }

    public function products()
    {
        return $this->belongsToMany(\App\Welkome\Product::class);
    }

    public function services()
    {
        return $this->belongsToMany(\App\Welkome\Service::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function company()
    {
        return $this->belongsTo(\App\Welkome\Company::class);
    }

    public function guests()
    {
        return $this->belongsToMany(\App\Welkome\Guest::class);
    }
}
