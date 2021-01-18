<div class="data mt-4">
    <div class="line mb-4"></div>
    <h1 class="text-uppercase">@lang('vouchers.voucher')</h1>
    @if ($voucher->reservation)
        <h2><small>(@lang('vouchers.reservation'))</small></h2>
    @else
        <h2><small>(@lang('transactions.' . $voucher->type))</small></h2>
    @endif

    <div class="data-box">
        <div class="data-separator d-block"></div>
        <h4 class="text-muted font-weight-light">No.</h4>
        <h4 class="font-weight-bold">{{ $voucher->number }}</h4>
    </div>
    <div class="data-box">
        <div class="data-separator d-block"></div>
        <h4 class="text-muted font-weight-light">@lang('common.date')</h4>
        <h4 class="font-weight-bold">{{ $voucher->created_at->format('Y-m-d') }}</h4>
    </div>

    @if ($voucher->type == 'lodging')
        <div class="data-box">
            <h5 class="font-weight-bold text-uppercase">@lang('vouchers.origin') / @lang('vouchers.destination')</h5>
            <p>{{ $voucher->origin }} - {{ $voucher->destination }}</p>
        </div>
    @endif

    @if ($voucher->type == 'lodging')
        <div class="data-box">
            <h5 class="font-weight-bold text-uppercase">@lang('payments.status')</h5>
            @if ($voucher->type == 'loss')
                <h6 class="font-weight-light">@lang('vouchers.losses')</h6>
                <p>$ {{ number_format($voucher->value - $voucher->payments->sum('value'), 2, '.', ',') }}</p>
            @else
                <h6 class="font-weight-light">Total</h6>
                <p>$ {{ number_format($voucher->value, 2, ',', '.') }}</p>
                <h6 class="font-weight-light">@lang('payments.title')</h6>
                <p>$ {{ number_format($voucher->payments->sum('value'), 2, ',', '.') }}</p>
                <h6 class="font-weight-light">@lang('common.percentage')</h6>
                <p>{{ round(($voucher->payments->sum('value') / $voucher->value) * 100, 2) }}%</p>
            @endif
        </div>
    @endif
    <div class="data-box text-center">
        <img class="img-fluid image" src="{{ empty($voucher->hotel->image) ? asset('/images/hotel.png') : asset(Storage::url($voucher->hotel->image)) }}" alt="{{ $voucher->hotel->business_name }}">
        <h3 class="font-weight-bold text-center">{{ $voucher->hotel->business_name }}</h3>
    </div>
</div>
