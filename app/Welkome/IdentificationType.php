<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;

class IdentificationType extends Model
{
    public function guest()
    {
        return $this->hasMany(Welkome\Guest::class, 'identification_type_id');
    }
}
