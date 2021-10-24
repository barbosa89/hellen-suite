<?php

namespace App\Actions;

use App\Contracts\Handleable;
use Illuminate\Database\Eloquent\Model;

abstract class Action implements Handleable
{
    protected array $data;
    protected ?Model $model;

    public function __construct(array $data, Model $model = null)
    {
        $this->data = $data;
        $this->model = $model;
    }

    /**
     * @param array $data
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \Illuminate\Database\Eloquent\Model|bool
     */
    public static function execute(array $data, Model $model = null)
    {
        $action = new static($data, $model);

        return $action->handle();
    }

    abstract public function handle();
}
