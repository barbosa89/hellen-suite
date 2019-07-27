<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Request;

class Breadcrumb
{
    /**
     * The link text.
     *
     * @var string
     */
	public $text;

	/**
     * The link URL.
     *
     * @var string
     */
	public $url;

	public static function get()
	{
		$breadcrumb = new Breadcrumb();
		$breadcrumb->setURI();
		$breadcrumb->setText();

		return $breadcrumb;
	}

	public function setURI()
	{
		$this->url = implode('/', Request::segments());
	}

	public function setText()
	{
		$this->text = trans(Request::segment(1) . '.title');
	}
}