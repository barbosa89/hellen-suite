<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdateLocaleRequest;
use App\Settings\GeneralSettings;
use Illuminate\Http\RedirectResponse;

class LocaleController extends Controller
{
    public function update(UpdateLocaleRequest $request): RedirectResponse
    {
        $locale = $request->string('locale')->toString();

        app()->setLocale($locale);
        session(['locale' => $locale]);

        $settings = app(GeneralSettings::class);
        $settings->language = $locale;
        $settings->save();

        return redirect()->back();
    }
}
