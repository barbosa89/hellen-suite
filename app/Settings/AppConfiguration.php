<?php

declare(strict_types=1);

namespace App\Settings;

class AppConfiguration
{
    public function __construct(private GeneralSettings $settings) {}

    /**
     * @return list<string>
     */
    public function missingSettings(): array
    {
        return collect([
            'currency' => $this->settings->currency,
        ])->filter(blank(...))
            ->keys()
            ->all();
    }
}
