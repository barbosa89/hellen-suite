<?php

namespace App\Helpers;

use Illuminate\Support\Collection;

class Response
{
    public function __construct(
        private readonly Collection $collection,
        private readonly ?string $format = null,
        private readonly ?string $template = null
    ) {}

    public function get(): array
    {
        if ($this->format == 'rendered' && view()->exists($this->template)) {
            return $this->render()->toArray();
        }

        return $this->collection->toArray();
    }

    private function render(): Collection
    {
        $rendered = collect();

        foreach ($this->collection as $value) {
            $render = view($this->template, compact('value'))->render();
            $rendered->push($render);
        }

        return $rendered;
    }
}
