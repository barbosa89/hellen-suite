<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Setting;
use Illuminate\View\View;
use App\Constants\Modules;
use Illuminate\Http\Request;
use App\Models\Configuration;

class HotelSettingController extends Controller
{
    public function index(string $hotel): View
    {
        $hotel = Hotel::findHash($hotel, fields_get('hotels'));
        $hotel->load('settings');

        $configurations = Configuration::where('module', Modules::HOTELS)
            ->enabled()
            ->get(['id', 'name', 'module']);

        return view('app.hotels.settings.index', compact('hotel', 'configurations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function show(Setting $setting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function edit(Setting $setting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Setting $setting)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Setting $setting)
    {
        //
    }
}
