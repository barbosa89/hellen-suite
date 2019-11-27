<div class="row mb-4">
    <div class="col-12 col-sx-12 col-sm-12 col-md-12 d-block d-lg-none d-xl-none mb-4">
        <div class="row">
            <div class="col-4 col-xs-4 col-sm-4 col-md-4 col-lg-3 col-xl-3">
                <a href="{{ route('hotels.show', ['room' => Hashids::encode($invoice->hotel->id)]) }}">
                    <img class="img-fluid" src="{{ empty($invoice->hotel->image) ? asset('/images/hotel.png') : asset(Storage::url($invoice->hotel->image)) }}" alt="{{ $invoice->hotel->business_name }}">
                </a>
            </div>
            <div class="col-8 col-xs-8 col-sm-8 col-md-8 col-lg-8 col-xl-8 align-self-center">
                <div class="row">
                    <div class="col-md-12">
                        <span class="d-block font-weight-bold">Hotel</span>
                        <span class="d-block">
                            <a href="{{ route('hotels.show', ['id' => Hashids::encode($invoice->hotel->id)]) }}">
                                {{ $invoice->hotel->business_name }}
                            </a>
                        </span>
                    </div>
                    <div class="col-md-12">
                        <span class="d-block font-weight-bold">NIT</span>
                        <span class="d-block">
                            <a href="{{ route('hotels.show', ['id' => Hashids::encode($invoice->hotel->id)]) }}">
                                {{ $invoice->hotel->tin }}
                            </a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sx-12 col-sm-12 col-md-12 col-lg-8 col-xl-8">
        <div class="row mb-4">
                <div class="col-6 col-sx-6 col-sm-6 col-md-3 col-lg-3 col-xl-3 align-self-center">
                    <span class="text-uppercase badge badge-dark text-wrap" style="font-size:24px">
                        @lang('invoices.invoice')

                        @if ($invoice->reservation)
                            <small>(Reservación)</small>
                        @endif
                    </span>
                </div>
                <div class="col-6 col-sx-6 col-sm-6 col-md-3 col-lg-3 col-xl-3">
                    <span class="d-block font-weight-light">No.</span>
                    <span class="d-block">
                        <a href="{{ route('invoices.show', ['id' => Hashids::encode($invoice->id)] ) }}">
                            {{ $invoice->number }}
                        </a>
                    </span>
                </div>
                <div class="col-6 col-sx-6 col-sm-6 col-md-3 col-lg-3 col-xl-3">
                    <span class="d-block font-weight-light">Fecha</span>
                    <span class="d-block">
                        {{ $invoice->created_at->format('Y-m-d') }}
                    </span>
                </div>
                <div class="col-6 col-sx-6 col-sm-6 col-md-3 col-lg-3 col-xl-3">
                    <span class="d-block font-weight-light">Valor</span>
                    <span class="d-block">
                        $ {{ number_format($invoice->value, 0, ',', '.') }}
                    </span>
                </div>
        </div>

        <div class="row">
            @if (!empty($customer))
                <div class="col-3 col-sx-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">
                    <span class="d-block font-weight-light">Cliente</span>
                    <span class="d-block">
                        <a href="{{ $customer['route'] }}">
                            {{ $customer['name'] }}
                        </a>
                    </span>
                </div>
                <div class="col-3 col-sx-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">
                    <span class="d-block font-weight-light">NIT / Documento</span>
                    <span class="d-block">
                        <a href="{{ $customer['route'] }}">
                            {{ $customer['tin'] }}
                        </a>
                    </span>
                </div>
                <div class="col-3 col-sx-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">
                    <span class="d-block font-weight-light">Origen / Destino</span>
                    <span class="d-block">
                        {{ $invoice->origin ?? 'No definido' }} - {{ $invoice->destination ?? 'No definido' }}
                    </span>
                </div>
                <div class="col-3 col-sx-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">
                    <span class="d-block font-weight-light">@lang('payments.title')</span>
                    <span class="d-block">
                        $ {{ number_format($invoice->payments->sum('value'), 0, '.', ',') }}
                    </span>
                </div>
            @endif
        </div>
    </div>
    <div class="col-lg-4 col-xl-4 d-none d-lg-block d-xl-block">
        <div class="row">
            <div class="col-md-8 align-self-center text-right">
                <div class="row">
                    <div class="col-md-12">
                        <span class="d-block font-weight-bold">Hotel</span>
                        <span class="d-block">
                            <a href="{{ route('hotels.show', ['id' => Hashids::encode($invoice->hotel->id)]) }}">
                                {{ $invoice->hotel->business_name }}
                            </a>
                        </span>
                    </div>
                    <div class="col-md-12">
                        <span class="d-block font-weight-bold">NIT</span>
                        <span class="d-block">
                            <a href="{{ route('hotels.show', ['id' => Hashids::encode($invoice->hotel->id)]) }}">
                                {{ $invoice->hotel->tin }}
                            </a>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-right">
                <a href="{{ route('hotels.show', ['room' => Hashids::encode($invoice->hotel->id)]) }}">
                    <img class="img-fluid" src="{{ empty($invoice->hotel->image) ? asset('/images/hotel.png') : asset(Storage::url($invoice->hotel->image)) }}" alt="{{ $invoice->hotel->business_name }}">
                </a>
            </div>
        </div>
    </div>
</div>