<div class="row">
    <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
        <h3>Hotel:</h3>
        <p>
            <a href="{{ route('hotels.show', ['id' => Hashids::encode($product->hotel->id)]) }}">
                {{ $product->hotel->business_name }}
            </a>
        </p>
    </div>
    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
        <h2>@lang('common.description'):</h2>
        <p>{{ $product->description }} <i class="fas fa-{{ $product->status ? 'check' : 'times-circle' }}"></i></p>
    </div>
    <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
        <h2>@lang('common.brand'):</h2>
        {{ $product->brand ?? trans('common.noData') }}
    </div>
</div>

<div class="row mb-4">
    <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
        <h3>@lang('common.reference'):</h3>
        <p>{{ $product->reference ?? trans('common.noData') }}</p>
    </div>
    <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
        <h3>@lang('common.quantity'):</h3>
        <p>{{ $product->quantity }}</p>
    </div>
    <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
        <h3>@lang('common.price'):</h3>
        <p>$ {{ round($product->price, 0) }}</p>
    </div>
</div>