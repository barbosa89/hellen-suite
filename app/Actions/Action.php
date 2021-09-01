<?php

namespace App\Actions;

use App\Contracts\Execute;

abstract class Action implements Execute
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model|bool
     */
    public static function run(array $data)
    {
        $action = new static($data);

        return $action->execute();
    }

    abstract public function execute();
}
