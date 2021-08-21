<?php

namespace App\Http\Controllers\Api;

use App\Models\Guest;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Guests\Index;
use App\Http\Controllers\Controller;

class GuestController extends Controller
{
    public function index(Index $request): JsonResponse
    {
        $guests = Guest::owner()
            ->latest()
            ->filter($request->validated())
            ->paginate($request->input('per_page', config('settings.paginate')));

        return response()->json($guests);
    }
}
