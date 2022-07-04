<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;

class PaymentGateway
{
    public Invoice $invoice;

    /**
     * Constructor
     *
     * @param \App\Models\Invoice $invoice
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Create self instance statically
     *
     * @param \App\Models\Invoice $invoice
     * @return self
     */
    public static function create(Invoice $invoice): self
    {
        return new PaymentGateway($invoice);
    }

    /**
     * Generate URL to redirect to payment gateway
     *
     * @return string
     */
    public function generatePaymentUrl(): string
    {
        return external_url(config('settings.payments.url'), [
            'public-key' => config('settings.payments.key'),
            'currency' => $this->invoice->currency->code,
            'amount-in-cents' => number_format($this->invoice->total, 2, '', ''),
            'reference' => $this->invoice->number,
            'redirect-url' => route('invoices.payments.confirm', ['number' => $this->invoice->number])
        ]);
    }

    /**
     * Return redirect to payment gateway
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect(): RedirectResponse
    {
        return redirect()->away($this->generatePaymentUrl());
    }

    /**
     * Get transaction status data to check payment
     *
     * @param string $id
     * @return \Illuminate\Http\Client\Response
     */
    public static function confirm(string $id): Response
    {
        return Http::get(config('settings.payments.confirm') . $id);
    }
}
