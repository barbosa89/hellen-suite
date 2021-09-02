<?php

namespace App\Services;

use App\Contracts\Gettable;
use App\Models\IdentificationType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class IdentificationTypeCache implements Gettable
{
    public function get(): Collection
    {
        return Cache::rememberForever('identification-types', function () {
            return IdentificationType::all(['id', 'type']);
        });
    }
}
