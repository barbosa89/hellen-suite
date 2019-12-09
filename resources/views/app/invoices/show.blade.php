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
                    'option' => trans('common.export'),
                    'url' => route('invoices.export')
                ],
                [
                    'type' => 'hideable',
                    'option' => trans('payments.title'),
                    'url' => route('payments.index', [
                            'invoice' => Hashids::encode($invoice->id)
                        ]),
                    'show' => !$invoice->losses
                ],
                [
                    'option' => trans('common.options'),
                    'type' => 'dropdown',
                    'url' => [
                        [
                            'type' => 'hideable',
                            'option' => trans('rooms.addRoom'),
                            'url' => route('invoices.rooms', [
                                'id' => Hashids::encode($invoice->id)
                            ]),
                            'show' => $invoice->open
                        ],
                        [
                            'type' => 'hideable',
                            'option' => $invoice->company ? trans('invoices.linkNewCompany') : trans('invoices.linkCompany'),
                            'url' => route('invoices.companies.search', [
                                'id' => Hashids::encode($invoice->id)
                            ]),
                            'show' => $invoice->open
                        ],
                        [
                            'type' => 'hideable',
                            'option' => trans('invoices.registerGuests'),
                            'url' => route('invoices.guests.search', [
                                'id' => Hashids::encode($invoice->id)
                            ]),
                            'show' => $invoice->open
                        ],
                        [
                            'type' => 'hideable',
                            'option' => trans('invoices.loadProducts'),
                            'url' => route('invoices.products', ['id' => Hashids::encode($invoice->id)]),
                            'show' => !$invoice->reservation and $invoice->open
                        ],
                        [
                            'type' => 'hideable',
                            'option' => trans('invoices.loadServices'),
                            'url' => route('invoices.services', ['id' => Hashids::encode($invoice->id)]),
                            'show' => !$invoice->reservation and $invoice->open
                        ],
                        [
                            'type' => 'hideable',
                            'option' => trans('common.register') . ' ' . trans('vehicles.vehicle'),
                            'url' => route('invoices.vehicles.search', ['id' => Hashids::encode($invoice->id)]),
                            'show' => !$invoice->reservation and $invoice->open
                        ],
                        [
                            'type' => 'hideable',
                            'option' => 'Agregar servicios de terceros',
                            'url' => route('invoices.external.add', ['id' => Hashids::encode($invoice->id)]),
                            'show' => !$invoice->reservation and $invoice->open
                        ],
                        [
                            'type' => 'hideable',
                            'option' => trans('invoices.addAdditional'),
                            'url' => route('invoices.additionals.create', ['id' => Hashids::encode($invoice->id)]),
                            'show' => !$invoice->reservation and $invoice->open
                        ],
                        [
                            'type' => 'hideable',
                            'option' => trans('invoices.close'),
                            'url' => route('invoices.close', [
                                'id' => Hashids::encode($invoice->id)
                            ]),
                            'show' => $invoice->open
                        ],
                        [
                            'type' => 'confirm',
                            'option' => trans('common.delete'),
                            'url' => route('invoices.destroy', [
                                'id' => Hashids::encode($invoice->id)
                            ]),
                            'method' => 'DELETE'
                        ],
                    ],
                ],
                [
                    'option' => trans('common.back'),
                    'url' => url()->previous()
                ],
            ]
        ])

        @include('app.invoices.info')

        @if($invoice->reservation)
            <a href="{{ route('invoices.reservation.checkin', ['id' => Hashids::encode($invoice->id)]) }}">
                <div class="alert alert-info alert-important mt-4">
                    <i class="fa fa-key"></i> {{ trans('common.register') }} {{ trans('invoices.checkin') }}
                </div>
            </a>
        @endif

        <!-- Company -->
        @if($invoice->company)
            <div class="row mb-6">
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
                                            <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                                <h5>@lang('companies.company')</h5>
                                            </div>
                                            <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                                                <h5>@lang('companies.tin')</h5>
                                            </div>
                                            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                                <h5>@lang('common.options')</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="crud-list-items">
                                        <div class="crud-list-row">
                                            <div class="row">
                                                <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 align-self-center">
                                                    <p>
                                                        <a href="{{ route('companies.show', ['id' => Hashids::encode($invoice->company->id)]) }}">
                                                            {{ $invoice->company->business_name }}
                                                        </a>
                                                    </p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5 align-self-center">
                                                    <p>
                                                        <a href="{{ route('companies.show', ['id' => Hashids::encode($invoice->company->id)]) }}">
                                                            {{ $invoice->company->tin }}
                                                        </a>
                                                    </p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 align-self-center">
                                                    <a href="#" class="btn btn-link" title="{{ trans('common.delete') }}" onclick="confirmRedirect(event, '{{ route('invoices.companies.remove', ['id' => Hashids::encode($invoice->id), 'company' => Hashids::encode($invoice->company->id)], false) }}')">
                                                        <i class="fas fa-times-circle"></i>
                                                    </a>
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

        <!-- Guests -->
        <div class="row mt-4">
            <div class="col-md-12">
                <h4 class="page-header">
                    <small><i class="fas fa-users"></i></small> @lang('guests.title')
                </h4>
                @if($invoice->guests->isEmpty())
                    <a href="{{ route('invoices.guests.search', ['id' => Hashids::encode($invoice->id)]) }}">
                        <div class="alert alert-info alert-important mt-4">
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
                                                    <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1 align-self-center">
                                                        <p>{{ strtoupper($guest->identificationType->type) }}</p>
                                                    </div>
                                                    <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
                                                        <p>
                                                            <a href="{{ route('guests.show', ['id' => Hashids::encode($guest->id)]) }}">
                                                                {{ number_format($guest->dni, 0, ',', '.') }}
                                                            </a>
                                                        </p>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 align-self-center">
                                                        <p>
                                                            <a href="{{ route('guests.show', ['id' => Hashids::encode($guest->id)]) }}">
                                                                {{ $guest->full_name }}
                                                            </a>
                                                        </p>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 align-self-center">
                                                        <p>{{ $assigned_room->number }}</p>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 align-self-center">
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
                                                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
                                                        <a class="btn btn-link" title="{{ trans('rooms.changeRoom') }}" href="#" onclick="confirmRedirect(event, '{{ route('invoices.guests.change.form', ['id' => Hashids::encode($invoice->id), 'guest' => Hashids::encode($guest->id)], false) }}')">
                                                            <i class="fas fa-sync-alt"></i>
                                                        </a>
                                                        <a class="btn btn-link" title="{{ trans('common.delete') }}" href="#" onclick="confirmRedirect(event, '{{ route('invoices.guests.remove', ['id' => Hashids::encode($invoice->id), 'guest' => Hashids::encode($guest->id)], false) }}')">
                                                            <i class="fas fa-user-times"></i>
                                                        </a>
                                                        @if ($invoice->guests->count() > 1)
                                                            <a class="btn btn-link" title="{{ trans($guest->status ? 'guests.registerExit' : 'guests.registerEntry') }}" href="{{ route('guests.toggle', ['id' => Hashids::encode($guest->id), 'invoice' => Hashids::encode($invoice->id)]) }}">
                                                                <i class="fas fa-door-{{ $guest->status ? 'closed' : 'open' }}"></i>
                                                            </a>
                                                        @endif
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

        <!-- Rooms -->
        @if($invoice->rooms->isNotEmpty())
            <div class="row mt-4">
                <div class="col-md-12">
                    <h4 class="page-header">
                        <small><i class="fas fa-bed"></i></small> @lang('rooms.title')
                    </h4>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="crud-list">
                                <div class="crud-list-heading">
                                    <div class="row">
                                        <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1">
                                            <h5><i class="fas fa-hashtag"></i></h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1">
                                            <h5>@lang('invoices.nights')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5>Inicio</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5>Finaliza</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5>@lang('common.value')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5>@lang('common.total')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5>@lang('common.options')</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="crud-list-items">
                                    @foreach($invoice->rooms as $room)
                                        <div class="crud-list-row">
                                            <div class="row">
                                                <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1 align-self-center">
                                                    <p>
                                                        <a href="{{ route('rooms.show', ['id' => Hashids::encode($room->id)]) }}">
                                                            {{ $room->number }}
                                                        </a>
                                                    </p>
                                                </div>
                                                <div class="col-xs-12 col-sm-11 col-md-1 col-lg-1 align-self-center">
                                                    <p>{{ $room->pivot->quantity }}</p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 align-self-center">
                                                    <p>{{ $room->pivot->start }}</p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 align-self-center">
                                                    <p>{{ $room->pivot->end }}</p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 align-self-center">
                                                    <p>
                                                        {{ number_format($room->price - $room->pivot->discount, 2, ',', '.') }}
                                                    </p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 align-self-center">
                                                    <p>{{  number_format($room->pivot->value, 2, ',', '.') }}</p>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
                                                    @if ($invoice->rooms->count() > 1)
                                                        <a class="btn btn-link" href="#" title="{{ trans('common.change') }}" onclick="confirmRedirect(event, '{{ route('invoices.rooms.change.form', ['id' => Hashids::encode($invoice->id), 'room' => Hashids::encode($room->id)], false) }}')">
                                                            <i class="fas fa-redo"></i>
                                                        </a>
                                                    @endif
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

        <!-- Products -->
        @if($invoice->products->isNotEmpty())
            <div class="row mt-4">
                <div class="col-md-12">
                    <h4 class="page-header">
                        <small><i class="fas fa-boxes"></i></small> @lang('products.title')
                    </h4>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="crud-list">
                                <div class="crud-list-heading">
                                    <div class="row">
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5>@lang('products.product')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5>@lang('common.value')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5>@lang('common.quantity')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5>@lang('common.total')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5>@lang('common.date')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5>@lang('common.options')</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="crud-list-items">
                                    @foreach($invoice->products as $product)
                                        <div class="crud-list-row">
                                            <div class="row">
                                                <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
                                                    <p>
                                                        <a href="{{ route('products.show', ['id' => Hashids::encode($product->id)]) }}">
                                                            {{ $product->description }}
                                                        </a>
                                                    </p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 align-self-center">
                                                    <p>
                                                        {{ number_format($product->price, 2, ',', '.') }}
                                                    </p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 align-self-center">
                                                    <p>{{ $product->pivot->quantity }}</p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 align-self-center">
                                                    <p>{{ number_format($product->pivot->value, 2, ',', '.') }}</p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 align-self-center">
                                                    <p>{{ $product->pivot->created_at->format('Y-m-d') }}</p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 align-self-center">
                                                    <a href="#" class="btn btn-link" onclick="confirmRedirect(event, '{{ route('invoices.products.remove', ['id' => Hashids::encode($invoice->id), 'product' => Hashids::encode($product->pivot->id)], false) }}')">
                                                        <i class="fas fa-times-circle"></i>
                                                    </a>
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
            <div class="row mt-4">
                <div class="col-md-12">
                    <h4 class="page-header">
                        <small><i class="fas fa-concierge-bell"></i></small> @lang('services.title')
                    </h4>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="crud-list">
                                <div class="crud-list-heading">
                                    <div class="row">
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5>@lang('services.service')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5>@lang('common.value')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5>@lang('common.quantity')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5>@lang('common.total')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5>@lang('common.date')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5>@lang('common.options')</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="crud-list-items">
                                    @foreach($invoice->services as $service)
                                        <div class="crud-list-row">
                                            <div class="row">
                                                <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
                                                    <p>
                                                        <a href="{{ route('services.show', ['id' => Hashids::encode($service->id)]) }}">
                                                            {{ $service->description }}
                                                        </a>
                                                    </p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 align-self-center">
                                                    <p>
                                                        {{ number_format($service->price, 2, ',', '.') }}
                                                    </p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 align-self-center">
                                                    <p>{{ $service->pivot->quantity }}</p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 align-self-center">
                                                    <p>{{ number_format($service->pivot->value, 2, ',', '.') }}</p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 align-self-center">
                                                    <p>{{ $service->pivot->created_at->format('Y-m-d') }}</p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 align-self-center">
                                                    <a href="#" class="btn btn-link" onclick="confirmRedirect(event, '{{ route('invoices.services.remove', ['id' => Hashids::encode($invoice->id), 'product' => Hashids::encode($service->pivot->id)], false) }}')">
                                                        <i class="fas fa-times-circle"></i>
                                                    </a>
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

        <!-- Additionals -->
        @if($invoice->additionals->where('billable', true)->count() > 0)
            <div class="row mt-4">
                <div class="col-md-12">
                    <h4 class="page-header">
                        <small><i class="fas fa-star"></i></small> @lang('invoices.additionals')
                    </h4>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="crud-list">
                                <div class="crud-list-heading">
                                    <div class="row">
                                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                            <h5>@lang('common.description')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5>@lang('common.value')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5>@lang('common.date')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5>@lang('common.options')</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="crud-list-items">
                                    @foreach($invoice->additionals->where('billable', true) as $additional)
                                        <div class="crud-list-row">
                                            <div class="row">
                                                <div class="col-xs-12 col-sm-11 col-md-6 col-lg-6 align-self-center">
                                                    <p>{{ $additional->description }}</p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 align-self-center">
                                                    <p>{{ number_format($additional->value, 2, '.', ',') }}</p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 align-self-center">
                                                    <p>{{ $additional->created_at->format('Y-m-d') }}</p>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
                                                    <a class="btn btn-link" href="#" title="{{ trans('common.delete') }}" onclick="confirmRedirect(event, '{{ route('invoices.additionals.remove', ['id' => Hashids::encode($invoice->id), 'additional' => Hashids::encode($additional->id)], false) }}')">
                                                        <i class="fas fa-times-circle"></i>
                                                    </a>
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
        <!-- Additionals -->

        <!-- External -->
        @if($invoice->additionals->where('billable', false)->count() > 0)
            <div class="row mt-4">
                <div class="col-md-12">
                    <h4 class="page-header">
                        <small><i class="fas fa-file-medical"></i></small> @lang('invoices.external')
                    </h4>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="crud-list">
                                <div class="crud-list-heading">
                                    <div class="row">
                                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                                            <h5>@lang('common.description')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5>@lang('common.value')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5>@lang('common.date')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                            <h5>@lang('common.options')</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="crud-list-items">
                                    @foreach($invoice->additionals->where('billable', false) as $external)
                                        <div class="crud-list-row">
                                            <div class="row">
                                                <div class="col-xs-12 col-sm-11 col-md-6 col-lg-6 align-self-center">
                                                    <p>{{ $external->description }}</p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 align-self-center">
                                                    <p>{{ number_format($external->value, 2, '.', ',') }}</p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 align-self-center">
                                                    <p>{{ $external->created_at->format('Y-m-d') }}</p>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
                                                    <a class="btn btn-link" href="#" title="{{ trans('common.delete') }}" onclick="confirmRedirect(event, '{{ route('invoices.additionals.remove', ['id' => Hashids::encode($invoice->id), 'additional' => Hashids::encode($external->id)], false) }}')">
                                                        <i class="fas fa-times-circle"></i>
                                                    </a>
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
        <!-- External -->

        <!-- Vehicles -->
        @if ($invoice->guests->isNotEmpty())
            @php
                $vehicles = 0;
                foreach($invoice->guests as $guest) {
                    $vehicles += $guest->vehicles->count();
                }
            @endphp

            @if ($vehicles > 0)
                <div class="row mt-4">
                    <div class="col-md-12">
                        <h4 class="page-header">
                            <small><i class="fas fa-car"></i></small> @lang('vehicles.title')
                        </h4>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="crud-list">
                                    <div class="crud-list-heading">
                                        <div class="row">
                                            <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                                <h5>@lang('vehicles.registration')</h5>
                                            </div>
                                            <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                                <h5>@lang('common.brand')</h5>
                                            </div>
                                            <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                                <h5>Color</h5>
                                            </div>
                                            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4">
                                                <h5>@lang('guests.guest')</h5>
                                            </div>
                                            <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                                <h5>@lang('common.options')</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="crud-list-items">
                                        @foreach($invoice->guests as $guest)
                                            @foreach($guest->vehicles as $vehicle)
                                                <div class="crud-list-row">
                                                    <div class="row">
                                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
                                                            <p>
                                                                <a href="{{ route('vehicles.show', ['id' => Hashids::encode($vehicle->id)]) }}">
                                                                    {{ strtoupper($vehicle->registration) }}
                                                                </a>
                                                            </p>
                                                        </div>
                                                        <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2 align-self-center">
                                                            <p>
                                                                {{ $vehicle->brand }}
                                                            </p>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 align-self-center">
                                                            <p>
                                                                {{ $vehicle->color }}
                                                            </p>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 align-self-center">
                                                            <p>
                                                                <a href="{{ route('guests.show', ['id' => Hashids::encode($guest->id)]) }}">
                                                                    {{ $guest->full_name }}
                                                                </a>
                                                            </p>
                                                        </div>
                                                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
                                                            <a class="btn btn-link" title="{{ trans('common.delete') }}" href="#" onclick="confirmRedirect(event, '{{ route('invoices.vehicles.remove', ['id' => Hashids::encode($invoice->id), 'vehicle' => Hashids::encode($vehicle->id), 'guest' => Hashids::encode($guest->id)], false) }}')">
                                                                <i class="fas fa-times-circle"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
        <!-- Vehicles -->

        @include('partials.spacer', ['size' => 'md'])
        @include('partials.modal-confirm')
    </div>

@endsection