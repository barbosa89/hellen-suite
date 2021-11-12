<?php

namespace App\Presenters;

use Illuminate\Support\Str;
use Laracasts\Presenter\Presenter as BasePresenter;

abstract class Presenter extends BasePresenter
{
    public function __get($property)
    {
        $method = Str::camel($property);

        if ($this->hasMethod($method)) {
            return $this->{$method}();
        }

        if ($this->hasMethod($property)) {
            return $this->{$property}();
        }

        return $this->entity->{$property};
    }

    private function hasMethod(string $method): bool
    {
        return method_exists($this, $method) && is_callable([$this, $method]);
    }
}
