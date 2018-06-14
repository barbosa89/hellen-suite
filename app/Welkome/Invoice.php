<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Invoice extends Model
{
    use LogsActivity;

    public function guests()
    {
        return $this->belongsToMany(Welkome\Guest::class);
    }

    public function rooms()
    {
        return $this->belongsToMany(Welkome\Room::class);
    }

    public function payments()
    {
        return $this->hasMany(Welkome\Payment::class);
    }

    public function products()
    {
        return $this->belongsToMany(Welkome\Product::class);
    }

    public function services()
    {
        return $this->belongsToMany(Welkome\Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
