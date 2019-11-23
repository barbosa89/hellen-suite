<?php

namespace App\Welkome;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Invoice extends Model
{
    use LogsActivity;

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

    public function hotel()
    {
        return $this->belongsTo(\App\Welkome\Hotel::class);
    }

    public function additionals()
    {
        return $this->hasMany(\App\Welkome\Additional::class);
    }
}
