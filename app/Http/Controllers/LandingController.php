<?php

namespace App\Http\Controllers;

use App\Models\Plan;

class LandingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $plans = Plan::allColumns()
            ->active()
            ->get();

        return view('landing', compact('plans'));
    }
}
