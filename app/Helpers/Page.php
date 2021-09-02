<?php

namespace App\Helpers;

class Page
{
    public static function step(): int
    {
        return request('per_page', config('settings.paginate'));
    }
}
