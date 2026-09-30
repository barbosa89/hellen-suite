<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Payment;
use App\Models\Stay;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

use function is_string;

class PaymentSupportController extends Controller
{
    public function __invoke(Hotel $hotel, Stay $stay, Payment $payment): StreamedResponse
    {
        $payment = Payment::query()
            ->whereHas(
                'folio',
                fn (Builder $query): Builder => $query->where('hotel_id', $hotel->id)
                    ->where('stay_id', $stay->id)
            )->findOrFail($payment->id);

        $path = $payment->getRawOriginal('support_path');

        abort_unless(is_string($path), 404);

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        return $disk->response($path);
    }
}
