<div class="row">
    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <h2>Hotel:</h2>
        <p>
            <a href="{{ route('hotels.show', ['id' => Hashids::encode($asset->hotel->id)]) }}">
                {{ $asset->hotel->business_name }}
            </a>
        </p>
    </div>
    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <h2>@lang('common.description'):</h2>
        <p>
            <a href="{{ route('assets.show', ['id' => Hashids::encode($asset->id)]) }}">
                {{ $asset->description }}
            </a>
        </p>
    </div>
    <div class="col-xs-4 col-sm-3 col-md-3 col-lg-3">
        <h2>@lang('common.brand'):</h2>
        {{ $asset->brand ?? trans('common.noData') }}
    </div>
    <div class="col-xs-4 col-sm-3 col-md-3 col-lg-3">
        <h3>@lang('common.number'):</h3>
        <p>{{ $asset->number }}</p>
    </div>
</div>

<div class="row">
    <div class="col-xs-4 col-sm-3 col-md-3 col-lg-3">
        <h3>@lang('common.model'):</h3>
        <p>{{ $asset->model ?? trans('common.noData') }}</p>
    </div>
    <div class="col-xs-4 col-sm-3 col-md-3 col-lg-3">
        <h3>@lang('assets.serialNumber'):</h3>
        <p>{{ $asset->serial_number ?? trans('common.noData') }}</p>
    </div>
    <div class="col-xs-4 col-sm-3 col-md-3 col-lg-3">
        <h3>@lang('common.location'):</h3>
        @if(empty($asset->location))
            @if($asset->room)
                <a href="{{ route('rooms.show', ['id' => Hashids::encode($asset->room->id)]) }}">
                    {{ trans('rooms.room') }} No. {{ $asset->room->number }}
                </a>
            @else
                {{ trans('common.noData') }}
            @endif
        @else
            {{ $asset->location }}
        @endif
    </div>
</div>
