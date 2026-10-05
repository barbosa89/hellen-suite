<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public null|string $currency = null;

    public null|string $language = null;

    public static function group(): string
    {
        return 'general';
    }
}
