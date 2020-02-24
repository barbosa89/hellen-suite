<div class="row">
    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
        <h2>@lang('common.description'):</h2>
        <p>{{ $prop->description }}</p>
    </div>
    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
        <h2>@lang('common.quantity'):</h2>
        {{ $prop->quantity }}
    </div>
    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
        <h2>@lang('common.value'):</h2>
        {{ number_format($prop->price, 2, ',', '.') }}
    </div>
    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
        <h3>@lang('common.model'):</h3>
        <p>{{ $prop->hotel->business_name }}</p>
    </div>
</div>