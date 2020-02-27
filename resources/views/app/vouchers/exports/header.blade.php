<!-- header -->
<div class="header">
    <div class="row">
        <div class="col-xs-6 from">
            <span class="d-block font-weight-light">@lang('vouchers.from'):</span>
            @if ($voucher->type == 'entry')
                @include('app.vouchers.exports.customer')
            @else
                @include('app.vouchers.exports.hotel')
            @endif
        </div>
        <div class="col-xs-6 to">
            <span class="d-block font-weight-light">@lang('vouchers.to'):</span>
            @if ($voucher->type == 'entry')
                @include('app.vouchers.exports.hotel')
            @else
                @include('app.vouchers.exports.customer')
            @endif
        </div>
    </div>
</div>
<!-- end header -->

<!-- note -->
<div class="row mt-4">
    <div class="col-xs-12">
        <p class="text-justify text-muted">
            <small>{{ $voucher->comments }}</small>
        </p>
    </div>
</div>
<!-- end note -->

<!-- items-header -->
<div class="items-header">
    <div class="row mt-4 items-header font-weight-bold">
        <div class="col-xs-12 mx-2">
            <div class="line"></div>
        </div>
        <div class="text-uppercase col-xs-6">@lang('common.description')</div>
        <div class="text-uppercase col-xs-2 text-center">@lang('common.price')</div>
        <div class="text-uppercase col-xs-2 text-center">@lang('common.quantity')</div>
        <div class="text-uppercase col-xs-2 text-right">TOTAL</div>
        <div class="text-uppercase col-xs-12 mx-2">
            <div class="line"></div>
        </div>
    </div>
</div>
<!-- end items-header -->