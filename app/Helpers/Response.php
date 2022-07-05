<?php

namespace App\Helpers;

use Illuminate\Support\Collection;

class Response
{
	private string $format;

	private string $template;

	private Collection $collection;

	public function __construct(Collection $collection, $format = null, $template = null)
	{
		$this->format = $format;
		$this->template = $template;
		$this->collection = $collection;
	}

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
