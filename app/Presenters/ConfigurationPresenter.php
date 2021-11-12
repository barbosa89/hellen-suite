<?php

namespace App\Presenters;

use App\Constants\Config;
use App\Constants\Modules;
use App\Presenters\Presenter;

class ConfigurationPresenter extends Presenter
{
    public function fullName(): string
    {
        return Config::trans($this->entity->name);
    }

    public function moduleName(): string
    {
        return Modules::trans($this->entity->module);
    }

    public function action(): string
    {
        return $this->entity->isEnabled() ? trans('common.disable') : trans('common.enable');
    }

    public function enabledAt(): string
    {
        return $this->entity->isEnabled()
            ? $this->entity->enabled_at->format('Y-m-d')
            : '';
    }
}
