@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('invoice', $invoice) }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('invoices.title'),
            'url' => route('invoices.index'),
            'options' => [
                [
                    'option' => trans('common.options'),
                    'type' => 'dropdown',
                    'url' => [
                        [
                            'option' => trans('rooms.addRoom'),
                            'url' => route('invoices.rooms', [
                                'id' => Hashids::encode($invoice->id)
                            ])
                        ],
                        [
                            'option' => 'Vincular empresa',
                            'url' => route('invoices.companies.search', [
                                'id' => Hashids::encode($invoice->id)
                            ]),
                        ],
                        [
                            'option' => trans('invoices.registerGuests'),
                            'url' => route('invoices.guests.search', [
                                'id' => Hashids::encode($invoice->id)
                            ])
                        ],
                        [
                            'option' => trans('invoices.loadProducts'),
                            'url' => route('invoices.products', ['id' => Hashids::encode($invoice->id)]),
                        ],
                        [
                            'option' => trans('invoices.loadServices'),
                            'url' => route('invoices.services', ['id' => Hashids::encode($invoice->id)]),
                        ],
                        [
                            'option' => 'Agregar servicios de terceros',
                            'url' => '#',
                        ],
                        [
                            'type' => 'divider'
                        ],
                        [
                        'option' => trans('common.close'),
                            'url' => "#"
                        ],
                        [
                            'type' => 'confirm',
                            'option' => trans('common.delete'),
                            'url' => route('invoices.destroy', [
                                'id' => Hashids::encode($invoice->id)
                            ]),
                            'method' => 'DELETE'
                        ],
                    ]
                ],
                [
                    'option' => trans('common.back'),
                    'url' => url()->previous()
                ],
            ]
        ])

        @include('app.invoices.info')

        <!-- Company -->
        @if($invoice->company)
            <div class="row mb-4">
                <div class="col-md-12">
                    <h3 class="page-header">
                        <small><i class="fas fa-building"></i></small> Empresa
                    </h3>
                    @if(empty($invoice->company))
                        <a href="{{ route('invoices.companies.search', ['id' => Hashids::encode($invoice->id)]) }}">
                            <div class="well">
                                <i class="fa fa-plus-circle"></i> @lang('common.register') {{ strtolower(trans('invoices.customerCompany')) }}
                            </div>
                        </a>
                    @else
                        <div class="row">
                            <div class="col-md-12">
                                <div class="crud-list">
                                    <div class="crud-list-heading">
                                        <div class="row">
                                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                                <h5>@lang('companies.company')</h5>
                                            </div>
                                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                                <h5>@lang('companies.tin')</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="crud-list-items">
                                        <div class="crud-list-row">
                                            <div class="row">
                                                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                                    <p>
                                                        <a href="{{ route('companies.show', ['id' => Hashids::encode($invoice->company->id)]) }}">
                                                            {{ $invoice->company->business_name }}
                                                        </a>
                                                    </p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 visible-md visible-lg">
                                                    <p>
                                                        <a href="{{ route('companies.show', ['id' => Hashids::encode($invoice->company->id)]) }}">
                                                            {{ $invoice->company->tin }}
                                                        </a>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
        <!-- Company -->

        <!-- Rooms -->
        @if($invoice->rooms->isNotEmpty())
            <div class="row mb-4">
                <div class="col-md-12">
                    <h4 class="page-header">
                        <small><i class="fas fa-bed"></i></small> @lang('rooms.title')
                    </h4>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="crud-list">
                                <div class="crud-list-heading">
                                    <div class="row">
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5><i class="fas fa-hashtag"></i></h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5>@lang('common.value')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5>Inicio</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5>Finaliza</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5>@lang('invoices.nights')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5>@lang('common.total')</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="crud-list-items">
                                    @foreach($invoice->rooms as $room)
                                        <div class="crud-list-row">
                                            <div class="row">
                                                <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                                    <p>
                                                        <a href="{{ route('rooms.show', ['id' => Hashids::encode($room->id)]) }}">
                                                            {{ $room->number }}
                                                        </a>
                                                    </p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                                                    <p>
                                                        <a href="{{ route('rooms.show', ['id' => Hashids::encode($room->id)]) }}">
                                                            {{ number_format($room->price, 2, ',', '.') }}
                                                        </a>
                                                    </p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                                                    <p>{{ $room->pivot->start }}</p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                                                    <p>{{ $room->pivot->end }}</p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                                                    <p>{{ $room->pivot->quantity }}</p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2">
                                                    <p>{{  number_format($room->pivot->value, 2, ',', '.') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <!-- Rooms -->

        <!-- Guests -->
        <div class="row mb-4">
            <div class="col-md-12">
                <h4 class="page-header">
                    <small><i class="fas fa-users"></i></small> @lang('guests.title')
                </h4>
                @if($invoice->guests->isEmpty())
                    <a href="{{ route('invoices.guests.search', ['id' => Hashids::encode($invoice->id)]) }}">
                        <div class="alert alert-info alert-important">
                            <i class="fa fa-plus-circle"></i> @lang('invoices.registerGuests')
                        </div>
                    </a>
                @else
                    <div class="row">
                        <div class="col-md-12">
                            <div class="crud-list">
                                <div class="crud-list-heading">
                                    <div class="row">
                                        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
                                            <h5>@lang('common.idNumber')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
                                            <h5>@lang('common.name')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5>@lang('rooms.room')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5>@lang('invoices.responsibleAdult')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5>@lang('common.options')</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="crud-list-items">
                                    @foreach($invoice->rooms as $assigned_room)
                                        @foreach($assigned_room->guests as $guest)
                                            <div class="crud-list-row">
                                                <div class="row">
                                                    <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1">
                                                        <p>{{ strtoupper($guest->identificationType->type) }}</p>
                                                    </div>
                                                    <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                                        <p>
                                                            <a href="{{ route('guests.show', ['id' => Hashids::encode($guest->id)]) }}">
                                                                {{ number_format($guest->dni, 0, ',', '.') }}
                                                            </a>
                                                        </p>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 visible-md visible-lg">
                                                        <p>
                                                            <a href="{{ route('guests.show', ['id' => Hashids::encode($guest->id)]) }}">
                                                                {{ $guest->full_name }}
                                                            </a>
                                                        </p>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 visible-md visible-lg">
                                                        <p>{{ $room->number }}</p>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 visible-md visible-lg">
                                                        @if(empty($guest->parent))
                                                            <p>-</p>
                                                        @else
                                                            <p>
                                                                <a href="{{ route('guests.show', ['id' => Hashids::encode($guest->parent->id)]) }}">
                                                                    {{ $guest->parent->full_name }}
                                                                </a>
                                                            </p>
                                                        @endif
                                                    </div>
                                                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                                        <a class="btn btn-link" href="#" data-room="{{ Hashids::encode($room->id) }}" data-guest="{{ Hashids::encode($guest->id) }}" data-invoice="{{ Hashids::encode($invoice->id) }}">
                                                            <i class="fas fa-times-circle"></i>
                                                        </a>
                                                        <a class="btn btn-link" href="#"><i class="fas fa-user-edit"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <!-- Guests -->

        <!-- Products -->
        @if($invoice->products->isNotEmpty())
            <div class="row">
                <div class="col-md-12">
                    <h4 class="page-header">
                        <small><i class="fas fa-boxes"></i></small> @lang('products.title')
                    </h4>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="crud-list">
                                <div class="crud-list-heading">
                                    <div class="row">
                                        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
                                            <h5>@lang('products.product')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
                                            <h5>@lang('common.value')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
                                            <h5>@lang('common.quantity')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
                                            <h5>@lang('common.total')</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="crud-list-items">
                                    @foreach($invoice->products as $product)
                                        <div class="crud-list-row">
                                            <div class="row">
                                                <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
                                                    <p>
                                                        <a href="{{ route('products.show', ['id' => Hashids::encode($product->id)]) }}">
                                                            {{ $product->description }}
                                                        </a>
                                                    </p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 visible-md visible-lg">
                                                    <p>
                                                        <a href="{{ route('products.show', ['id' => Hashids::encode($product->id)]) }}">
                                                            {{ number_format($product->price, 2, ',', '.') }}
                                                        </a>
                                                    </p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 visible-md visible-lg">
                                                    <p>{{ $product->pivot->quantity }}</p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 visible-md visible-lg">
                                                    <p>{{ number_format($product->pivot->value, 2, ',', '.') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <!-- Products -->

        <!-- Services -->
        @if($invoice->services->isNotEmpty())
            <div class="row">
                <div class="col-md-12">
                    <h4 class="page-header">
                        <small><i class="fas fa-washer"></i></small> @lang('services.title')
                    </h4>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="crud-list">
                                <div class="crud-list-heading">
                                    <div class="row">
                                        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
                                            <h5>@lang('services.service')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
                                            <h5>@lang('common.value')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
                                            <h5>@lang('common.quantity')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
                                            <h5>@lang('common.total')</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="crud-list-items">
                                    @foreach($invoice->services as $service)
                                        <div class="crud-list-row">
                                            <div class="row">
                                                <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
                                                    <p>
                                                        <a href="{{ route('services.show', ['id' => Hashids::encode($service->id)]) }}">
                                                            {{ $service->description }}
                                                        </a>
                                                    </p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 visible-md visible-lg">
                                                    <p>
                                                        {{ number_format($service->price, 2, ',', '.') }}
                                                    </p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 visible-md visible-lg">
                                                    <p>{{ $service->pivot->quantity }}</p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 visible-md visible-lg">
                                                    <p>{{ number_format($service->pivot->value, 2, ',', '.') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <!-- Services -->

        @include('partials.spacer', ['size' => 'md'])
        @include('partials.modal-confirm')
    </div>

@endsection