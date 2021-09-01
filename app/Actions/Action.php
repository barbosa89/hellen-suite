<?php

namespace App\Actions;

use App\Contracts\Execute;
use Illuminate\Database\Eloquent\Model;

abstract class Action implements Execute
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public static function run(array $data): Model
    {
        $action = new static($data);

        return $action->execute();
    }

    abstract public function execute();
}
