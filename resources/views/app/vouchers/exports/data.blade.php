<div class="col-xs-4 data py-4">
    <div class="spacer-lg">&nbsp;</div>
    <div class="line mb-4"></div>
    <h1 class="text-uppercase">@lang('common.invoice')</h1>

    <div class="data-box">
        <div class="data-separator d-block"></div>
        <h4 class="text-muted font-weight-light">No.</h4>
        <h4 class="font-weight-bold">{{ $voucher->number }}</h4>
        @if ($voucher->reservation)
            <h4><small>(@lang('vouchers.reservation'))</small></h4>
        @endif
    </div>
    <div class="data-box">
        <div class="data-separator d-block"></div>
        <h4 class="text-muted font-weight-light">@lang('common.date')</h4>
        <h4 class="font-weight-bold">{{ $voucher->created_at->format('Y-m-d') }}</h4>
    </div>
    <div class="data-box">
        <h5 class="font-weight-bold text-uppercase">@lang('vouchers.origin') / @lang('vouchers.destination')</h5>
        <p>{{ $voucher->origin }} - {{ $voucher->destination }}</p>
    </div>
    <div class="data-box">
        <h5 class="font-weight-bold text-uppercase">@lang('payments.status')</h5>
        @if ($voucher->losses)
            <h6 class="font-weight-light">@lang('vouchers.losses')</h6>
            <p>$ {{ number_format($voucher->value - $voucher->payments->sum('value'), 2, '.', ',') }}</p>
        @else
            <h6 class="font-weight-light">Total</h6>
            <p>$ {{ number_format($voucher->value, 2, ',', '.') }}</p>
            <h6 class="font-weight-light">@lang('payments.title')</h6>
            <p>$ {{ number_format($voucher->payments->sum('value'), 2, ',', '.') }}</p>
            <h6 class="font-weight-light">@lang('common.percentage')</h6>
            <p>{{ ($voucher->payments->sum('value') / $voucher->value) * 100 }}%</p>
        @endif
        <img class="img-fluid image" src="{{ empty($voucher->hotel->image) ? asset('/images/hotel.png') : asset(Storage::url($voucher->hotel->image)) }}" alt="{{ $voucher->hotel->business_name }}">
    </div>
    <div class="data-box align-text-bottom">
        <h1 class="text-center">Hotel</h1>
        <h2 class="font-weight-bold text-center">{{ $voucher->hotel->business_name }}</h2>
    </div>
</div>