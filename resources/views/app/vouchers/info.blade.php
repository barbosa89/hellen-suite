<div class="row mb-4">
    <div class="col-12 col-sx-12 col-sm-12 col-md-12 d-block d-lg-none d-xl-none mb-4">
        <div class="row">
            <div class="col-4 col-xs-4 col-sm-4 col-md-4 col-lg-3 col-xl-3">
                <a href="{{ route('hotels.show', ['id' => Hashids::encode($voucher->hotel->id)]) }}">
                    <img class="img-fluid" src="{{ empty($voucher->hotel->image) ? asset('/images/hotel.png') : asset(Storage::url($voucher->hotel->image)) }}" alt="{{ $voucher->hotel->business_name }}">
                </a>
            </div>
            <div class="col-8 col-xs-8 col-sm-8 col-md-8 col-lg-8 col-xl-8 align-self-center">
                <div class="row">
                    <div class="col-md-12">
                        <span class="d-block font-weight-bold">Hotel</span>
                        <span class="d-block">
                            <a href="{{ route('hotels.show', ['id' => Hashids::encode($voucher->hotel->id)]) }}">
                                {{ $voucher->hotel->business_name }}
                            </a>
                        </span>
                    </div>
                    <div class="col-md-12">
                        <span class="d-block font-weight-bold">NIT</span>
                        <span class="d-block">
                            <a href="{{ route('hotels.show', ['id' => Hashids::encode($voucher->hotel->id)]) }}">
                                {{ $voucher->hotel->tin }}
                            </a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sx-12 col-sm-12 col-md-12 col-lg-9 col-xl-9">
        <div class="row mb-4">
            <div class="col-6 col-sx-6 col-sm-6 col-md-3 col-lg-3 col-xl-3 align-self-center">
                <span class="text-uppercase badge badge-dark text-wrap" style="font-size:24px">
                    @lang('common.invoice')

                    @if ($voucher->reservation)
                        <small>(@lang('vouchers.reservation'))</small>
                    @endif
                </span>
            </div>
            <div class="col-6 col-sx-6 col-sm-6 col-md-2 col-lg-2 col-xl-2">
                <span class="d-block font-weight-light">No.</span>
                <span class="d-block">
                    <a href="{{ route('vouchers.show', ['id' => Hashids::encode($voucher->id)] ) }}">
                        {{ $voucher->number }}
                    </a>
                </span>
            </div>
            <div class="col-6 col-sx-6 col-sm-6 col-md-3 col-lg-3 col-xl-3">
                <span class="d-block font-weight-light">Fecha</span>
                <span class="d-block">
                    {{ $voucher->created_at->format('Y-m-d') }}
                </span>
            </div>
            <div class="col-6 col-sx-6 col-sm-6 col-md-2 col-lg-2 col-xl-2">
                <span class="d-block font-weight-light">Valor</span>
                <span class="d-block">
                    $ {{ number_format($voucher->value, 0, ',', '.') }}
                </span>
            </div>
            <div class="col-6 col-sx-6 col-sm-6 col-md-2 col-lg-2 col-xl-2">
                <span class="d-block font-weight-light text-center">@lang('common.status')</span>
                <span class="d-block text-center">
                    <i class="fa fa-{{ $voucher->open ? 'lock-open' : 'lock' }} fa-2x"></i>
                </span>
            </div>
        </div>

        <div class="row">
            @if (!empty($customer))
                <div class="col-3 col-sx-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">
                    <span class="d-block font-weight-light">Cliente</span>
                    <span class="d-block dont-break-out">
                        <a href="{{ $customer['route'] }}">
                            {{ $customer['name'] }}
                        </a>
                    </span>
                </div>
                <div class="col-2 col-sx-2 col-sm-2 col-md-2 col-lg-2 col-xl-2">
                    <span class="d-block font-weight-light">@lang('common.number')</span>
                    <span class="d-block">
                        <a href="{{ $customer['route'] }}">
                            {{ $customer['tin'] }}
                        </a>
                    </span>
                </div>
                <div class="col-3 col-sx-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">
                    <span class="d-block font-weight-light">Origen / Destino</span>
                    <span class="d-block">
                        {{ $voucher->origin ?? 'No definido' }} - {{ $voucher->destination ?? 'No definido' }}
                    </span>
                </div>
                <div class="col-2 col-sx-2 col-sm-2 col-md-2 col-lg-2 col-xl-2">
                    <span class="d-block font-weight-light">@lang('payments.title')</span>
                    <span class="d-block">
                        $ {{ number_format($voucher->payments->sum('value'), 0, '.', ',') }}
                    </span>
                </div>
                <div class="col-6 col-sx-6 col-sm-6 col-md-2 col-lg-2 col-xl-2 text-center">
                    @if ($voucher->losses)
                        <span class="d-block font-weight-light text-center">@lang('vouchers.losses')</span>
                        <span class="d-block text-center">
                            {{ number_format($voucher->value - $voucher->payments->sum('value'), 2, '.', ',') }}
                        </span>
                    @else
                        @if ($voucher->payment_method == false and $voucher->value > $voucher->payments->sum('value'))
                            <span class="d-block font-weight-light text-center">@lang('vouchers.losses')</span>
                            <span class="d-block text-center">
                                @can('vouchers.losses')
                                    <a href="#" title="{{ trans('vouchers.loss') }}" class="btn btn-danger btn-sm" onclick="confirmRedirect(event, '{{ route('vouchers.losses', ['id' => Hashids::encode($voucher->id)], false) }}')">
                                        <i class="fas fa-arrow-down"></i> <i class="fas fa-dollar-sign"></i>
                                    </a>
                                @endcan
                            </span>
                        @endif
                    @endif
                </div>
            @endif
        </div>
    </div>
    <div class="col-lg-3 col-xl-3 d-none d-lg-block d-xl-block">
        <div class="row">
            <div class="col-md-7 align-self-center text-right">
                <div class="row">
                    <div class="col-md-12 dont-break-out">
                        <span class="d-block font-weight-bold">Hotel</span>
                        <span class="d-block">
                            <a href="{{ route('hotels.show', ['id' => Hashids::encode($voucher->hotel->id)]) }}">
                                {{ $voucher->hotel->business_name }}
                            </a>
                        </span>
                    </div>
                    <div class="col-md-12 dont-break-out">
                        <span class="d-block font-weight-bold">NIT</span>
                        <span class="d-block">
                            <a href="{{ route('hotels.show', ['id' => Hashids::encode($voucher->hotel->id)]) }}">
                                {{ $voucher->hotel->tin }}
                            </a>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-5 text-right dont-break-out">
                <a href="{{ route('hotels.show', ['id' => Hashids::encode($voucher->hotel->id)]) }}">
                    <img class="img-fluid" src="{{ empty($voucher->hotel->image) ? asset('/images/hotel.png') : asset(Storage::url($voucher->hotel->image)) }}" alt="{{ $voucher->hotel->business_name }}">
                </a>
            </div>
        </div>
    </div>
</div>
