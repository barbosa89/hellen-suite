<?php

declare(strict_types=1);

namespace App\Actions\Payments;

use App\Actions\Stays\SummarizeStayFolio;
use App\Models\Payment;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;

use function base64_encode;
use function is_string;
use function str_starts_with;

final class BuildPaymentVoucherData
{
    public function __construct(private SummarizeStayFolio $summarizeStayFolio) {}

    /** @return array<string, mixed> */
    public function execute(Payment $payment): array
    {
        $payment->loadMissing([
            'voucher',
            'recordedBy:id,name',
            'folio.hotel',
            'folio.stay.responsibleGuest.identificationType',
        ]);

        $voucher = $payment->voucher;
        $hotel = $payment->folio->hotel;
        $stay = $payment->folio->stay;
        $responsibleGuest = $stay->responsibleGuest;
        $summary = $this->summarizeStayFolio->execute($stay);
        $locale = app()->getLocale();

        $folios = collect($summary['folios'])->map(function (array $folio) use ($payment): array {
            return [
                ...$folio,
                'is_payment_folio' => $folio['id'] === $payment->stay_folio_id,
                'rooms_text' => collect($folio['rooms'])->pluck('number')->join(' → '),
                'charge_total' => $this->money($folio['posted_charge_minor'], $folio['currency']),
                'adjustment_total' => $this->money($folio['adjustment_minor'], $folio['currency']),
                'charges' => collect($folio['charges'])->map(fn (array $charge): array => [
                    ...$charge,
                    'unit_amount' => $this->money($charge['unit_amount_minor'], $folio['currency']),
                    'total_amount' => $this->money($charge['total_amount_minor'], $folio['currency']),
                ])->all(),
                'adjustments' => collect($folio['adjustments'])->map(fn (array $adjustment): array => [
                    ...$adjustment,
                    'amount' => $this->money($adjustment['amount_minor'], $folio['currency']),
                ])->all(),
            ];
        })->all();

        return [
            'number' => $voucher->number,
            'code' => 'CP-' . str_pad((string) $voucher->number, 6, '0', STR_PAD_LEFT),
            'hotel' => [
                'business_name' => $hotel->business_name,
                'tin' => $hotel->tin,
                'address' => $hotel->address,
                'phone' => $hotel->phone,
                'mobile' => $hotel->mobile,
                'email' => $hotel->email,
                'logo' => $this->logoDataUri($hotel->getRawOriginal('image')),
            ],
            'responsible' => [
                'name' => "{$responsibleGuest->first_name} {$responsibleGuest->last_name}",
                'identification_type' => $responsibleGuest->identificationType->code->value,
                'identification_number' => $responsibleGuest->identification_number,
                'mobile' => $responsibleGuest->mobile,
                'email' => $responsibleGuest->email,
            ],
            'stay' => [
                'id' => $stay->id,
                'checked_in_at' => $stay->checked_in_at->locale($locale)->translatedFormat('d M Y, H:i'),
                'checked_out_at' => $stay->checked_out_at?->locale($locale)->translatedFormat('d M Y, H:i'),
            ],
            'payment' => [
                'id' => $payment->id,
                'folio_id' => $payment->stay_folio_id,
                'method' => $payment->method->value,
                'amount' => $this->money($payment->amount_minor, $payment->currency),
                'amount_minor' => $payment->amount_minor,
                'currency' => $payment->currency,
                'comment' => $payment->comment,
                'paid_at' => $payment->paid_at->locale($locale)->translatedFormat('d M Y, H:i'),
                'recorded_by' => $payment->recordedBy?->name,
                'has_support' => is_string($payment->getRawOriginal('support_path')),
            ],
            'folios' => $folios,
            'totals' => [
                'charges' => $this->money($summary['total_charge_minor'], $payment->currency),
                'adjustments' => $this->money($summary['total_adjustment_minor'], $payment->currency),
                'paid' => $this->money($summary['total_paid_minor'], $payment->currency),
                'balance' => $this->money($summary['balance_minor'], $payment->currency),
            ],
        ];
    }

    private function money(int $minor, string $currency): string
    {
        $locale = app()->getLocale() === 'es' ? 'es_CO' : 'en_US';

        return Number::currency($minor / 100, in: $currency, locale: $locale);
    }

    private function logoDataUri(mixed $path): null|string
    {
        if (! is_string($path) || str_starts_with($path, 'http')) {
            return null;
        }

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        if (! $disk->exists($path)) {
            return null;
        }

        $mimeType = $disk->mimeType($path);

        return is_string($mimeType)
            ? "data:{$mimeType};base64," . base64_encode($disk->get($path))
            : null;
    }
}
