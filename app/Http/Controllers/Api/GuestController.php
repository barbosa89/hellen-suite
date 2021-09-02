<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Page;
use App\Models\Guest;
use Illuminate\Http\JsonResponse;
use App\Actions\Guests\CreateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Guests\IndexRequest;
use App\Http\Requests\Guests\StoreRequest;

class GuestController extends Controller
{
    public function index(IndexRequest $request): JsonResponse
    {
        $guests = Guest::owner()
            ->latest()
            ->filter($request->validated())
            ->paginate(Page::step());

        return response()->json($guests);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        return response()->json(CreateAction::run($request->validated()));
    }
}
