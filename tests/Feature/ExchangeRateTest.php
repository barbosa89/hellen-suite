<?php

namespace Tests\Feature;

use App\Services\ExchangeRate;

use Tests\TestCase;

class ExchangeRateTest extends TestCase
{
    public function test_convert_usd_to_cop()
    {
        $currency = ExchangeRate::convert(1, 'USD', 'COP');

        $this->assertTrue(is_numeric($currency));
        $this->assertTrue((float) $currency > 0);
    }
}
