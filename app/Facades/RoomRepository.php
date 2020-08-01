<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class RoomRepository extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'RoomRepository';
    }
}
