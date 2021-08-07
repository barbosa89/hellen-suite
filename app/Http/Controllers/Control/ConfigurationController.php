<?php

namespace App\Http\Controllers\Control;

use Illuminate\View\View;
use App\Models\Configuration;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;

class ConfigurationController extends Controller
{
    public function index(): View
    {
        $configurations = Configuration::all(['id', 'name', 'enabled_at']);

        return view('control.configurations.index', compact('configurations'));
    }

    public function toggle(string $id)
    {
        $configuration = Configuration::hash($id)
            ->first(['id', 'name', 'enabled_at']);

        if (empty($configuration->enabled_at)) {
            flash(trans('configurations.toggle.enabled'))->success();

            $configuration->enabled_at = Carbon::now();
        } else {
            flash(trans('configurations.toggle.disabled'))->success();

            $configuration->enabled_at = null;
        }

        $configuration->save();

        return redirect()->route('configurations.index');
    }
}
