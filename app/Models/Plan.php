<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    public const FREE = 'FREE';

    public const BASIC = 'BASIC';

    public const PREMIUM = 'PREMIUM';

    public const PARTNER = 'PARTNER';
}
