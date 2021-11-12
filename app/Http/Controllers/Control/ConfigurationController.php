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
        $configurations = Configuration::all(['id', 'name', 'module', 'enabled_at']);

        return view('control.configurations.index', compact('configurations'));
    }

    public function toggle(string $id)
    {
        $configuration = Configuration::hash($id)
            ->first(['id', 'name', 'enabled_at']);

        $configuration->toggle();
        $configuration->save();

        flash(trans('common.updated.successfully'))->success();

        return redirect()->route('configurations.index');
    }
}
