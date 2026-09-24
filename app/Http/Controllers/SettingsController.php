<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Alcohol\ISO4217;
use App\Http\Requests\UpdateGeneralSettingsRequest;
use App\Settings\AppConfiguration;
use App\Settings\GeneralSettings;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function edit(GeneralSettings $settings, AppConfiguration $configuration, ISO4217 $iso4217): Response
    {
        $currencies = collect($iso4217->getAll())
            ->sortBy('alpha3')
            ->map(fn (array $currency): array => [
                'code' => $currency['alpha3'],
                'name' => $currency['name'],
            ])
            ->values()
            ->all();

        return Inertia::render('Settings/Edit', [
            'currency' => $settings->currency,
            'currencies' => $currencies,
            'missingSettings' => $configuration->missingSettings(),
        ]);
    }

    public function update(UpdateGeneralSettingsRequest $request, GeneralSettings $settings): RedirectResponse
    {
        $settings->currency = $request->string('currency')->toString();
        $settings->save();

        return redirect()->route('settings.edit')
            ->with('success', trans('settings.messages.updated'));
    }
}
