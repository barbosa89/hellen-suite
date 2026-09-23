<?php

declare(strict_types=1);

namespace Tests;

use App\Settings\GeneralSettings;

abstract class OperationalTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        GeneralSettings::fake([
            'currency' => 'COP',
        ]);
    }
}
