<?php

namespace App\Http\Controllers\Control;

use Illuminate\View\View;
use App\Models\Configuration;
use App\Http\Controllers\Controller;

class ConfigurationController extends Controller
{
    public function index(): View
    {
        $configurations = Configuration::all(['id', 'name', 'enabled_at']);

        return view('control.configurations.index', compact('configurations'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
