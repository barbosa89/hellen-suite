<?php

namespace App\Http\Controllers;

use App\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function locale($locale)
    {
        if (in_array($locale, ['en', 'es'])) {
            App::setLocale($locale);
        }
    }
}
