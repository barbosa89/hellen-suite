<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Http\JsonResponse;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(string $hotelHash): JsonResponse
    {
        $notes = Note::owner()
            ->whereHotel($hotelHash)
            ->latest()
            ->paginate(
                config('settings.paginate'),
                Note::getColumnNames(['user_id', 'hotel_id'])
            );

        return response()->json([
            'notes' => $notes,
        ]);
    }
}
