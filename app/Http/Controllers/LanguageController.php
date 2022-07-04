<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    public function locale($locale)
    {
        if (in_array($locale, ['en', 'es'])) {
            App::setLocale($locale);
        }
    }
}
