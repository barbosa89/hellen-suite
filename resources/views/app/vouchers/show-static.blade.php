@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('voucher', $voucher) }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('vouchers.title'),
            'url' => route('vouchers.index'),
            'options' => [
                [
                    'option' => trans('common.export'),
                    'url' => route('vouchers.export', ['id' => id_encode($voucher->id)]),
                    'permission' => 'vouchers.show'
                ],
                [
                    'type' => 'hideable',
                    'option' => trans('payments.title'),
                    'url' => route('payments.index', [
                            'voucher' => id_encode($voucher->id)
                        ]),
                    'show' => !$voucher->type != 'loss',
                    'permission' => 'payments.index'
                ],
                [
                    'option' => trans('common.back'),
                    'url' => route('vouchers.index')
                ],
            ]
        ])

        @include('app.vouchers.info')

        <!-- Company -->
        @if($voucher->company)
            <div class="row mb-6">
                <div class="col-md-12">
                    <h3 class="page-header">
                        <small><i class="fas fa-building"></i></small> Empresa
                    </h3>
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
                                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 align-self-center">
                                                <p>
                                                    <a href="{{ route('companies.show', ['id' => id_encode($voucher->company->id)]) }}">
                                                        {{ $voucher->company->business_name }}
                                                    </a>
                                                </p>
                                            </div>
                                            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 align-self-center">
                                                <p>
                                                    <a href="{{ route('companies.show', ['id' => id_encode($voucher->company->id)]) }}">
                                                        {{ $voucher->company->tin }}
                                                    </a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <!-- Company -->

        <!-- Guests -->
        @if ($voucher->guests->isNotEmpty())
            <div class="row mt-4">
                <div class="col-md-12">
                    <h4 class="page-header">
                        <small><i class="fas fa-users"></i></small> @lang('guests.title')
                    </h4>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="crud-list">
                                <div class="crud-list-heading">
                                    <div class="row">
                                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                            <h5>@lang('common.idNumber')</h5>
                                        </div>
                                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                            <h5>@lang('common.name')</h5>
                                        </div>
                                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                            <h5>@lang('rooms.room')</h5>
                                        </div>
                                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                            <h5>@lang('vouchers.responsibleAdult')</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="crud-list-items">
                                    @foreach($voucher->guests as $guest)
                                            <div class="crud-list-row">
                                                <div class="row">
                                                    <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1 align-self-center">
                                                        <p>{{ strtoupper($guest->identificationType->type) }}</p>
                                                    </div>
                                                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
                                                        <p>
                                                            <a href="{{ route('guests.show', ['id' => id_encode($guest->id)]) }}">
                                                                {{ $guest->dni }}
                                                            </a>
                                                        </p>
                                                    </div>
                                                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 align-self-center">
                                                        <p>
                                                            <a href="{{ route('guests.show', ['id' => id_encode($guest->id)]) }}">
                                                                {{ $guest->full_name }}
                                                            </a>
                                                        </p>
                                                    </div>
                                                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
                                                        <p>{{ $guest->rooms->first()->number }}</p>
                                                    </div>
                                                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 align-self-center">
                                                        @if(empty($guest->parent))
                                                            <p>-</p>
                                                        @else
                                                            <p>
                                                                <a href="{{ route('guests.show', ['id' => id_encode($guest->parent->id)]) }}">
                                                                    {{ $guest->parent->full_name }}
                                                                </a>
                                                            </p>
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
        <!-- Guests -->

        <!-- Rooms -->
        @if($voucher->rooms->isNotEmpty())
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
                                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                            <h5><i class="fas fa-hashtag"></i></h5>
                                        </div>
                                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                            <h5>@lang('vouchers.nights')</h5>
                                        </div>
                                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                            <h5>@lang('common.startDate')</h5>
                                        </div>
                                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                            <h5>@lang('common.endDate')</h5>
                                        </div>
                                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                            <h5>@lang('common.value')</h5>
                                        </div>
                                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                            <h5>@lang('common.total')</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="crud-list-items">
                                    @foreach($voucher->rooms as $room)
                                        <div class="crud-list-row">
                                            <div class="row">
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
                                                    <p>
                                                        <a href="{{ route('rooms.show', ['id' => id_encode($room->id)]) }}">
                                                            {{ $room->number }}
                                                        </a>
                                                    </p>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
                                                    <p>{{ $room->pivot->quantity }}</p>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
                                                    <p>{{ $room->pivot->start }}</p>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
                                                    <p>{{ $room->pivot->end }}</p>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
                                                    <p>
                                                        {{  number_format($room->pivot->value, 2, ',', '.') }}
                                                    </p>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
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

        <!-- Products -->
        @if($voucher->products->isNotEmpty())
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
                                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                            <h5>@lang('products.product')</h5>
                                        </div>
                                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                            <h5>@lang('common.value')</h5>
                                        </div>
                                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                            <h5>@lang('common.quantity')</h5>
                                        </div>
                                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                            <h5>@lang('common.total')</h5>
                                        </div>
                                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                            <h5>@lang('common.date')</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="crud-list-items">
                                    @foreach($voucher->products as $product)
                                        <div class="crud-list-row">
                                            <div class="row">
                                                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 align-self-center">
                                                    <p>
                                                        <a href="{{ route('products.show', ['id' => id_encode($product->id)]) }}">
                                                            {{ $product->description }}
                                                        </a>
                                                    </p>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
                                                    <p>
                                                        {{ number_format($product->price, 2, ',', '.') }}
                                                    </p>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
                                                    <p>{{ $product->pivot->quantity }}</p>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
                                                    <p>{{ number_format($product->pivot->value, 2, ',', '.') }}</p>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
                                                    <p>{{ $product->pivot->created_at->format('Y-m-d') }}</p>
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
        @if($voucher->services->where('is_dining_service', false)->isNotEmpty())
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
                                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                            <h5>@lang('services.service')</h5>
                                        </div>
                                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                            <h5>@lang('common.value')</h5>
                                        </div>
                                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                            <h5>@lang('common.quantity')</h5>
                                        </div>
                                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                            <h5>@lang('common.total')</h5>
                                        </div>
                                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                            <h5>@lang('common.date')</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="crud-list-items">
                                    @foreach($voucher->services->where('is_dining_service', false) as $service)
                                        <div class="crud-list-row">
                                            <div class="row">
                                                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 align-self-center">
                                                    <p>
                                                        <a href="{{ route('services.show', ['id' => id_encode($service->id)]) }}">
                                                            {{ $service->description }}
                                                        </a>
                                                    </p>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
                                                    <p>
                                                        {{ number_format($service->price, 2, ',', '.') }}
                                                    </p>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
                                                    <p>{{ $service->pivot->quantity }}</p>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
                                                    <p>{{ number_format($service->pivot->value, 2, ',', '.') }}</p>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
                                                    <p>{{ $service->pivot->created_at->format('Y-m-d') }}</p>
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

        <!-- Dining Services -->
        @if($voucher->services->where('is_dining_service', true)->isNotEmpty())
            <div class="row mt-4">
                <div class="col-md-12">
                    <h4 class="page-header">
                        <small><i class="fas fa-utensils"></i></small> @lang('dining.title')
                    </h4>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="crud-list">
                                <div class="crud-list-heading">
                                    <div class="row">
                                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                            <h5>@lang('dining.item')</h5>
                                        </div>
                                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                            <h5>@lang('common.value')</h5>
                                        </div>
                                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                            <h5>@lang('common.quantity')</h5>
                                        </div>
                                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                            <h5>@lang('common.total')</h5>
                                        </div>
                                        <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                                            <h5>@lang('common.date')</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="crud-list-items">
                                    @foreach($voucher->services->where('is_dining_service', true) as $service)
                                        <div class="crud-list-row">
                                            <div class="row">
                                                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 align-self-center">
                                                    <p>
                                                        <a href="{{ route('services.show', ['id' => id_encode($service->id)]) }}">
                                                            {{ $service->description }}
                                                        </a>
                                                    </p>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
                                                    <p>
                                                        {{ number_format($service->price, 2, ',', '.') }}
                                                    </p>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
                                                    <p>{{ $service->pivot->quantity }}</p>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
                                                    <p>{{ number_format($service->pivot->value, 2, ',', '.') }}</p>
                                                </div>
                                                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 align-self-center">
                                                    <p>{{ $service->pivot->created_at->format('Y-m-d') }}</p>
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
        <!-- Dining Services -->

        <!-- Props -->
        @if($voucher->props->isNotEmpty())
            <div class="row mt-4">
                <div class="col-md-12">
                    <h4 class="page-header">
                        <small><i class="fas fa-fw fa-person-booth"></i></small> @lang('props.title')
                    </h4>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="crud-list">
                                <div class="crud-list-heading">
                                    <div class="row">
                                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                            <h5>@lang('common.description')</h5>
                                        </div>
                                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                            <h5>@lang('common.quantity')</h5>
                                        </div>
                                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                            <h5>@lang('common.value')</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="crud-list-items">
                                    @foreach($voucher->props as $prop)
                                        <div class="crud-list-row">
                                            <div class="row">
                                                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 align-self-center">
                                                    <p>
                                                        <a href="{{ route('props.show', ['id' => id_encode($prop->id)]) }}">
                                                            {{ $prop->description }}
                                                        </a>
                                                    </p>
                                                </div>
                                                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 align-self-center">
                                                    <p>
                                                        {{ number_format($prop->pivot->value, 2, ',', '.') }}
                                                    </p>
                                                </div>
                                                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 align-self-center">
                                                    <p>{{ $prop->pivot->quantity }}</p>
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
        <!-- Props -->

        <!-- Additionals -->
        @if($voucher->additionals->where('billable', true)->count() > 0)
            <div class="row mt-4">
                <div class="col-md-12">
                    <h4 class="page-header">
                        <small><i class="fas fa-star"></i></small> @lang('vouchers.additionals')
                    </h4>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="crud-list">
                                <div class="crud-list-heading">
                                    <div class="row">
                                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                            <h5>@lang('common.description')</h5>
                                        </div>
                                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                            <h5>@lang('common.value')</h5>
                                        </div>
                                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                            <h5>@lang('common.date')</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="crud-list-items">
                                    @foreach($voucher->additionals->where('billable', true) as $additional)
                                        <div class="crud-list-row">
                                            <div class="row">
                                                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 align-self-center">
                                                    <p>{{ $additional->description }}</p>
                                                </div>
                                                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 align-self-center">
                                                    <p>{{ number_format($additional->value, 2, '.', ',') }}</p>
                                                </div>
                                                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 align-self-center">
                                                    <p>{{ $additional->created_at->format('Y-m-d') }}</p>
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
        @if($voucher->additionals->where('billable', false)->count() > 0)
            <div class="row mt-4">
                <div class="col-md-12">
                    <h4 class="page-header">
                        <small><i class="fas fa-file-medical"></i></small> @lang('vouchers.external')
                    </h4>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="crud-list">
                                <div class="crud-list-heading">
                                    <div class="row">
                                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                            <h5>@lang('common.description')</h5>
                                        </div>
                                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                            <h5>@lang('common.value')</h5>
                                        </div>
                                        <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                            <h5>@lang('common.date')</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="crud-list-items">
                                    @foreach($voucher->additionals->where('billable', false) as $external)
                                        <div class="crud-list-row">
                                            <div class="row">
                                                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 align-self-center">
                                                    <p>{{ $external->description }}</p>
                                                </div>
                                                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 align-self-center">
                                                    <p>{{ number_format($external->value, 2, '.', ',') }}</p>
                                                </div>
                                                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 align-self-center">
                                                    <p>{{ $external->created_at->format('Y-m-d') }}</p>
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
        @if ($voucher->guests->isNotEmpty())
            @php
                $vehicles = 0;
                foreach($voucher->guests as $guest) {
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
                                            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                                <h5>@lang('vehicles.registration')</h5>
                                            </div>
                                            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                                <h5>@lang('common.brand')</h5>
                                            </div>
                                            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                                <h5>Color</h5>
                                            </div>
                                            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                                                <h5>@lang('guests.guest')</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="crud-list-items">
                                        @foreach($voucher->guests as $guest)
                                            @foreach($guest->vehicles as $vehicle)
                                                <div class="crud-list-row">
                                                    <div class="row">
                                                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 align-self-center">
                                                            <p>
                                                                {{ strtoupper($vehicle->registration) }}
                                                            </p>
                                                        </div>
                                                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 align-self-center">
                                                            <p>
                                                                {{ $vehicle->brand }}
                                                            </p>
                                                        </div>
                                                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 align-self-center">
                                                            <p>
                                                                {{ $vehicle->color }}
                                                            </p>
                                                        </div>
                                                        <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 align-self-center">
                                                            <p>
                                                                <a href="{{ route('guests.show', ['id' => id_encode($guest->id)]) }}">
                                                                    {{ $guest->full_name }}
                                                                </a>
                                                            </p>
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

        @include('partials.modal-confirm')
    </div>

@endsection
