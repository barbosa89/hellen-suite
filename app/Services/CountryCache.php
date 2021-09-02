<?php

namespace App\Services;

use App\Models\Country;
use App\Contracts\Gettable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CountryCache implements Gettable
{
    public function get(): Collection
    {
        return Cache::rememberForever('countries', function () {
            return Country::all(['id', 'name']);
        });
    }
}
