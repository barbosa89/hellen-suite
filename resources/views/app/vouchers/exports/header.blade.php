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
