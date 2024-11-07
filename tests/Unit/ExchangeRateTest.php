<?php

namespace Tests\Unit;

use App\Services\ExchangeRate;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ExchangeRateTest extends TestCase
{
    public function test_convert_usd_to_cop(): void
    {
        Http::fake(function ($request) {
            return Http::response(json_encode([
                'USD_COP' => 3000,
            ]), Response::HTTP_OK);
        });

        $currency = ExchangeRate::convert(1, 'USD', 'COP');

        $this->assertTrue(is_numeric($currency));
        $this->assertEquals('3000', $currency);
    }
}
