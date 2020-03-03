<div class="row">
    <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
        <h3>Hotel:</h3>
        <p>
            <a href="{{ route('hotels.show', ['id' => Hashids::encode($service->hotel->id)]) }}">
                {{ $service->hotel->business_name }}
            </a>
        </p>
    </div>
    <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
        <h2>@lang('common.description'):</h2>
        <p>{{ $service->description }} <i class="fas fa-{{ $service->status ? 'check' : 'times-circle' }}"></i></p>
    </div>
    <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
        <h2>@lang('common.brand'):</h2>
        {{ round($service->price, 0) }}
    </div>
</div>