@extends('layouts.app')

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('invoices.title'),
            'url' => route('invoices.index'),
            'options' => [
                [
                    'option' => trans('common.options'),
                    'url' => [
                        [
                            'option' => trans('rooms.addRoom'),
                            'url' => route('invoices.rooms', [
                                'id' => Hashids::encode($invoice->id)
                            ])
                        ],
                        [
                            'type' => 'hideable',
                            'option' => trans('invoices.registerCompany'),
                            'url' => route('invoices.companies.search', [
                                'id' => Hashids::encode($invoice->id)
                            ]),
                            'show' => empty($invoice->company) ? true : false
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
                                'room' => Hashids::encode($invoice->id)
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

        @if($invoice->for_company)
            <div class="row">
                <div class="col-md-12">
                    <h3 class="page-header">@lang('invoices.customerCompany')</h3>
                    @if(empty($invoice->company))
                        <a href="{{ route('invoices.companies.search', ['id' => Hashids::encode($invoice->id)]) }}">
                            <div class="well">
                                <i class="fa fa-plus-circle"></i> @lang('common.register') {{ strtolower(trans('invoices.customerCompany')) }}
                            </div>
                        </a>
                    @else
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                            <p>{{ $invoice->company->dni }}</p>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                            <p>{{ $invoice->company->name }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- <div class="row">
            <div class="col-md-12">
                <h3 class="page-header">@lang('invoices.customerGuest')</h3>
                @if(empty($customer))
                <a href="{{ route('invoices.guests.search', ['room' => Hashids::encode($invoice->id)]) }}">
                        <div class="well">
                            <i class="fa fa-plus-circle"></i> @lang('common.register') {{ strtolower(trans('invoices.customerGuest')) }}
                        </div>
                    </a>
                @else
                    <div class="crud-list">
                        <div class="crud-list-heading">
                            <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
                                    <h5>@lang('common.idNumber')</h5>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-9 col-lg-9">
                                    <h5>@lang('common.name')</h5>
                                </div>
                            </div>
                        </div>
                        <div class="crud-list-items">
                            <div class="crud-list-row">
                                <div class="row">
                                    <div class="col-xs-6 col-sm-6 col-md-1 col-lg-1"> 
                                        <p>{{ strtoupper($customer->identificationType->type) }}</p>
                                    </div>
                                    <div class="col-xs-6 col-sm-6 col-md-2 col-lg-2">
                                        <p>
                                            <a href="{{ route('guests.show', ['id' => Hashids::encode($customer->id)]) }}">
                                                {{ number_format($customer->dni, 0, ',', '.') }}
                                            </a>
                                        </p>
                                    </div>
                                    <div class="col-xs-12 col-sm-12 col-md-9 col-lg-9 visible-md visible-lg">
                                        <p>
                                            <a href="{{ route('guests.show', ['id' => Hashids::encode($customer->id)]) }}">
                                                {{ $customer->name . ' ' . $customer->last_name }}
                                            </a>
                                        </p>            
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div> --}}

        <!-- Rooms -->
        @if(!$invoice->rooms->isEmpty())
            <div class="row">
                <div class="col-md-12">
                    <h3 class="page-header">@lang('rooms.title')</h3>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="crud-list">
                                <div class="crud-list-heading">
                                    <div class="row">
                                        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
                                            <h5>@lang('rooms.room')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
                                            <h5>@lang('common.value')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
                                            <h5>@lang('invoices.nights')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
                                            <h5>@lang('common.total')</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="crud-list-items">
                                    @foreach($invoice->rooms as $room)
                                        <div class="crud-list-row">
                                            <div class="row">
                                                <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
                                                    <p>
                                                        <a href="{{ route('rooms.show', ['id' => Hashids::encode($room->id)]) }}">
                                                            {{ $room->number }}
                                                        </a>
                                                    </p>
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 visible-md visible-lg">
                                                    <p>
                                                        <a href="{{ route('rooms.show', ['id' => Hashids::encode($room->id)]) }}">
                                                            {{ number_format($room->price, 2, ',', '.') }}
                                                        </a>
                                                    </p>            
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 visible-md visible-lg">
                                                    <p>{{ $room->pivot->quantity }}</p>            
                                                </div>
                                                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 visible-md visible-lg">
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
        <div class="row">
            <div class="col-md-12">
                <h3 class="page-header">@lang('guests.title')</h3>
                @if($invoice->guests->isEmpty())
                    <a href="{{ route('invoices.rooms', ['room' => Hashids::encode($invoice->id)]) }}">
                        <div class="well">
                            <i class="fa fa-plus-circle"></i> @lang('rooms.addRoom')
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
                                        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
                                            <h5>@lang('rooms.room')</h5>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-3 col-lg-3">
                                            <h5>@lang('invoices.responsibleAdult')</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="crud-list-items">
                                    @foreach($invoice->rooms as $room)
                                        @foreach($room->guests as $guest)
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
                                                                {{ $guest->name . ' ' . $guest->last_name }}
                                                                @if($guest->id === $customer->id)
                                                                    <i class="fa fa-street-view"></i>
                                                                @endif
                                                            </a>
                                                        </p>            
                                                    </div>
                                                    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 visible-md visible-lg">
                                                        <p>{{ $room->number }}</p>            
                                                    </div>
                                                    <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 visible-md visible-lg">
                                                        @if(empty($guest->parent))
                                                            <p>-</p>
                                                        @else
                                                            <p>
                                                                <a href="{{ route('guests.show', ['id' => Hashids::encode($guest->parent->id)]) }}">
                                                                    {{ $guest->parent->name . ' ' . $guest->parent->last_name }}
                                                                </a>
                                                            </p>
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

        <!-- Products -->
        @if(!$invoice->products->isEmpty())
            <div class="row">
                <div class="col-md-12">
                    <h3 class="page-header">@lang('products.title')</h3>
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
        @if(!$invoice->services->isEmpty())
            <div class="row">
                <div class="col-md-12">
                    <h3 class="page-header">@lang('services.title')</h3>
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