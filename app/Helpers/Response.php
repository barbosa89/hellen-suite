<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Vinkla\Hashids\Facades\Hashids;

class Response
{
	/**
     * The response format
     *
     * @var string
     */
	private $format;

    /**
     * Template name for rendered format.
     *
     * @var array
     */
	private $template;

    /**
     * A data collection.
     *
     * @var array
     */
	private $collection;

	/**
     * Construct method.
     *
	 * @param string $format
	 * @param string $template
	 * @param \Illuminate\Support\Collection $collection
     * @return array
     */
	public function __construct($format = null, $template = null, Collection $collection)
	{
		$this->format = $format;
		$this->template = $template;
		$this->collection = $collection;
	}

    /**
     * Parse data results by format request.
     *
     * @return array
     */
    public function get()
    {
        if ($this->format == 'rendered' and view()->exists($this->template)) {
            return $this->render();
        }

        return $this->collection->toArray();
    }

    /**
     * Render data collection in array.
     *
     * @return array
     */
    private function render()
    {
        $rendered = collect();

		foreach ($this->collection as $key => $value) {
			$render = view($this->template, compact('value'))->render();
			$rendered->push($render);
		}

        return $rendered;
    }
}