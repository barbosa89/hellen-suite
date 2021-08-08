<?php

namespace App\Presenters;

use App\Constants\Config;
use Laracasts\Presenter\Presenter;

class ConfigurationPresenter extends Presenter
{
    public function fullName(): string
    {
        return Config::trans($this->name);
    }

    public function enabledAt(): string
    {
        return $this->enabled_at
            ? $this->enabled_at->format('Y-m-d')
            : '';
    }
}
