<?php

use App\Helpers\Breadcrumb;

if (!function_exists('show_route')) {
    function breadcrumb()
    {
        return Breadcrumb::get();
    }
}