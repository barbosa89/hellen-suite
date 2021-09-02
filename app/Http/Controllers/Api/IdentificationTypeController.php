<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Services\IdentificationTypeCache;

class IdentificationTypeController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json((new IdentificationTypeCache())->get());
    }
}
