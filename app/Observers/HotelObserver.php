<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Hotel;
use Illuminate\Support\Facades\Storage;

class HotelObserver
{
    public function deleted(Hotel $hotel): void
    {
        $image = $hotel->getRawOriginal('image');

        if ($image) {
            Storage::disk('public')->delete($image);
        }
    }
}
