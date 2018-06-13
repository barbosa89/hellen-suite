<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    public function children()
    {
        return $this->hasMany(Welkome\Guest::class, 'responsible_of');
    }

    public function parent()
    {
        return $this->belongsTo(Welkome\Guest::class, 'responsible_of');
    }

    public function company()
    {
        return $this->belongsTo(Welkome\Company::class);
    }

    public function invoices()
    {
        return $this->belongsToMany(Welkome\Invoice::class);
    }

    public function identificationType()
    {
        return $this->belongsTo(Welkome\IdentificationType::class, 'identification_type_id');
    }

    public function vehicle()
    {
        return $this->hasMany(Welkome\Vehicle::class);
    }
}
