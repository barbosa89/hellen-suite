<?php

namespace App\Http\Controllers\Api;

use App\Services\CountryCache;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class CountryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json((new CountryCache())->get());
    }
}
