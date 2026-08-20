<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateLocaleRequest;
use Illuminate\Http\RedirectResponse;

class LocaleController extends Controller
{
    public function update(UpdateLocaleRequest $request): RedirectResponse
    {
        $locale = $request->string('locale')->toString();

        app()->setLocale($locale);
        session(['locale' => $locale]);

        return redirect()->back();
    }
}
