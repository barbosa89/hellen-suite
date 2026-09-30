<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Hotel;
use Illuminate\Support\Facades\Storage;

class HotelObserver
{
    public function deleting(Hotel $hotel): void
    {
        $hotel->stayFolios()
            ->with('payments:id,stay_folio_id,support_path')
            ->get()
            ->flatMap->payments
            ->map(fn ($payment) => $payment->getRawOriginal('support_path'))
            ->filter()
            ->each(fn (string $path) => Storage::disk('public')->delete($path));
    }

    public function deleted(Hotel $hotel): void
    {
        $image = $hotel->getRawOriginal('image');

        if ($image) {
            Storage::disk('public')->delete($image);
        }
    }
}
