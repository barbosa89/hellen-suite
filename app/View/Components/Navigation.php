<?php

namespace App\View\Components;

use Illuminate\View\View;
use Illuminate\View\Component;

class Navigation extends Component
{
    public string $title;
    public string $url;
    public bool $hotelSelect;

    public function __construct(string $title, string $url, bool $hotelSelect = false)
    {
        $this->title = $title;
        $this->url = $url;
        $this->hotelSelect = $hotelSelect;
    }

    public function render(): View
    {
        return view('components.navigation');
    }
}
