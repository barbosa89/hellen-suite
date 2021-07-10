<?php

namespace App\Contracts;

interface Buildable
{
    public function build(): self;
}
