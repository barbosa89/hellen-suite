@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('guest', $guest) }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('guests.title'),
            'url' => route('guests.index'),
            'options' => [
                [
                    'type' => 'dropdown',
                    'option' => trans('common.options'),
                    'url' => [
                        [
                            'option' => trans('common.edit'),
                            'url' => route('guests.edit', [
                                'id' => Hashids::encode($guest->id)
                            ]),
                        ],
                        [
                            'type' => 'confirm',
                            'option' => trans('common.delete.item'),
                            'url' => route('guests.destroy', [
                                'id' => Hashids::encode($guest->id)
                            ]),
                            'method' => 'DELETE'
                        ],
                    ]
                ],
                [
                    'option' => trans('common.new'),
                    'url' => route('guests.create')
                ],
                [
                    'option' => trans('common.back'),
                    'url' => route('guests.index')
                ],
            ]
        ])

        <div class="row">
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                <h3>@lang('common.name'):</h3>
                <p>
                    <a href="{{ route('guests.show', ['id' => Hashids::encode($guest->id)]) }}">
                        {{ $guest->full_name }}
                    </a>
                    @switch($guest->gender)
                        @case('f')
                            <i class="fas fa-2x fa-female"></i>
                            @break
                        @case(2)
                            <i class="fas fa-2x fa-male"></i>
                            @break
                        @default
                            <i class="fas fa-2x fa-rainbow"></i>
                    @endswitch
                </p>
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                <h3>@lang('common.' . $guest->identificationType->type):</h3>
                <p>{{ $guest->dni }}</p>
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                <h3>@lang('common.email'):</h3>
                {{ $guest->email ?? trans('common.noData') }}
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                <h3>@lang('common.address'):</h3>
                <p>{{ $guest->address ?? trans('common.noData') }}</p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                <h3>@lang('common.phone'):</h3>
                <p>{{ $guest->phone ?? trans('common.noData') }}</p>
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                <h3>@lang('common.birthdate'):</h3>
                <p>{{ $guest->birthdate ?? trans('common.noData') }}</p>
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                <h3>@lang('guests.profession'):</h3>
                <p>{{ $guest->profession ?? trans('common.noData') }}</p>
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                <h3>@lang('guests.country'):</h3>
                <p>{{ $guest->country->name }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                @include('partials.list', [
                    'data' => $guest->vouchers,
                    'listHeading' => 'app.guests.vouchers.list-heading',
                    'listRow' => 'app.guests.vouchers.list-row'
                ])
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection
