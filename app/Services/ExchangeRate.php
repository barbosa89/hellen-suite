<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

/**
 * @method static string convert()
 */
class ExchangeRate
{
    /**
     * Binary currency converter
     *
     * @param float $value
     * @param string $from
     * @param string $to
     * @return string
     */
    public static function convert(float $value, string $from, string $to): string
    {
        $pair = "{$from}_{$to}";

        $response = Http::get(config('settings.currency.url'), [
            'q' => $pair,
            'compact' => 'ultra',
            'apiKey' => config('settings.currency.key')
        ]);

        if ($response->ok()) {
            return $response->json()[$pair];
        }

        throw new Exception(trans('currencies.exchange.error'), 1);
    }
}
