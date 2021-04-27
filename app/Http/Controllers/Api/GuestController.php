<?php

namespace App\Http\Controllers\Api;

use App\Models\Guest;
use Illuminate\Http\Request;
use App\Contracts\GuestRepository;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;

class GuestController extends Controller
{
    public GuestRepository $guest;

    public function __construct(GuestRepository $guest)
    {
        $this->guest = $guest;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $validated = request()->validate([
            'from_date' => 'bail|nullable|date|before_or_equal:today',
            'status' => 'bail|nullable|string|in:is_staying,is_not_staying',
            'query_by' => 'bail|nullable|alpha_num|min:3|max:30',
        ]);

        $guests = $this->guest->paginate(
            request()->get('per_page', 15),
            Arr::only($validated, Guest::SCOPE_FILTERS),
        );

        return response()->json([
            'guests' => $guests,
        ]);
    }
}
