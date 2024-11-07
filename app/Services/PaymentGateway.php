<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Http\Client\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;

class PaymentGateway
{
    public Invoice $invoice;

    /**
     * Constructor
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Create self instance statically
     */
    public static function create(Invoice $invoice): self
    {
        return new PaymentGateway($invoice);
    }

    /**
     * Generate URL to redirect to payment gateway
     */
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

    /**
     * Return redirect to payment gateway
     */
    public function redirect(): RedirectResponse
    {
        return redirect()->away($this->generatePaymentUrl());
    }

    /**
     * Get transaction status data to check payment
     */
    public static function confirm(string $id): Response
    {
        return Http::get(config('settings.payments.confirm').$id);
    }
}
