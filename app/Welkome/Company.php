<?php

namespace App\Welkome;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    public function guest()
    {
        return $this->hasMany(Welkome\Guest::class);
    }
}
