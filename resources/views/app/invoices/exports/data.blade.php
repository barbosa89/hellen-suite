<div class="col-xs-4 data py-4 col-equal">
    <div class="spacer-lg">&nbsp;</div>
    <div class="line mb-4"></div>
    <h1 class="text-uppercase">@lang('invoices.invoice')</h1>

    <div class="data-box">
        <div class="data-separator d-block"></div>
        <h4 class="text-muted font-weight-light">No.</h4>
        <h4 class="font-weight-bold">{{ $invoice->number }}</h4>
    </div>
    <div class="data-box">
        <div class="data-separator d-block"></div>
        <h4 class="text-muted font-weight-light">@lang('common.date')</h4>
        <h4 class="font-weight-bold">{{ $invoice->created_at->format('Y-m-d') }}</h4>
    </div>
    <div class="data-box">
        <h5 class="font-weight-bold">TERMS</h5>
        <p>Mollit elit reprehenderit consectetur cupidatat anim qui deserunt duis. Veniam laboris id veniam in eu.</p>
    </div>
    <div class="data-box">
        <h5 class="font-weight-bold">PAYMENT METHODS</h5>
        <h6 class="font-weight-light">PAYPAL</h6>
        <p>paypal@example.com</p>
        <h6 class="font-weight-light">ACCOUNTING NUMBER</h6>
        <p>1234567890988</p>
        <h6 class="font-weight-light">QR CODE</h6>
        <img src="{{ asset('/images/qr.png') }}" alt="" class="img-fluid qr-code">
    </div>
    <div class="data-box align-text-bottom">
        <h1 class="font-weight-bold text-center">{{ $invoice->hotel->business_name }}</h1>
    </div>
</div>