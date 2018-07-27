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
    public function toSearchableArray()
    {
        $array = $this->toArray();

        return $array;
    }

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

    public function guest()
    {
        return $this->belongsTo(\App\Welkome\Guest::class);
    }
}
