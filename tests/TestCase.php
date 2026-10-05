<?php

declare(strict_types=1);

namespace Tests;

use App\Settings\GeneralSettings;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        GeneralSettings::fake([
            'currency' => 'COP',
            'language' => config('app.locale'),
        ]);
    }
}
