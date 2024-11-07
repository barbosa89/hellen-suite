<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Http\Client\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;

class PaymentGateway
{
    public function __construct(public Invoice $invoice)
    {
    }

    public static function create(Invoice $invoice): self
    {
        return new PaymentGateway($invoice);
    }

    public function generatePaymentUrl(): string
    {
        return external_url(config('settings.payments.url'), [
            'public-key' => config('settings.payments.key'),
            'currency' => $this->invoice->currency->code,
            'amount-in-cents' => number_format($this->invoice->total, 2, '', ''),
            'reference' => $this->invoice->number,
            'redirect-url' => route('invoices.payments.confirm', ['number' => $this->invoice->number]),
        ]);
    }

    public function redirect(): RedirectResponse
    {
        return redirect()->away($this->generatePaymentUrl());
    }

    public static function confirm(string $id): Response
    {
        return Http::get(config('settings.payments.confirm').$id);
    }
}
