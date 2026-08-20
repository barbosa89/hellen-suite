<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HotelImageController extends Controller
{
    public function show(Hotel $hotel): StreamedResponse
    {
        $path = $hotel->getRawOriginal('image');

        abort_unless($path && Storage::disk('public')->exists($path), StreamedResponse::HTTP_NOT_FOUND);

        return Storage::disk('public')->response($path, null, [
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
