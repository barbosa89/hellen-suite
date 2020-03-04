@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('room', $room) }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('rooms.title'),
            'url' => route('rooms.index'),
            'search' => [
                'action' => route('rooms.search')
            ],
            'options' => [
                [
                    'option' => trans('common.options'),
                    'type' => 'dropdown',
                    'url' => [
                        // [
                        //     'option' => trans('assets.add'),
                        //     'url' => '#'
                        // ],
                        // [
                        //     'option' => trans('products.add'),
                        //     'url' => '#'
                        // ],
                        // [
                        //     'option' => 'Asignar',
                        //     'url' => '#'
                        // ],
                        // [
                        //     'option' => 'Marca como inhabilitada',
                        //     'url' => '#'
                        // ],
                        // [
                        //     'option' => 'Está en mantenimiento',
                        //     'url' => '#'
                        // ],
                        [
                            'option' => trans('common.edit'),
                            'url' => route('rooms.edit', [
                                'id' => Hashids::encode($room->id)
                            ]),
                        ],
                        [
                            'type' => 'confirm',
                            'option' => trans('common.delete.item'),
                            'url' => route('rooms.destroy', [
                                'id' => Hashids::encode($room->id)
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

        <div class="row">
            <div class="col-xs-3 col-sm-3 col-md-3 col-md-3">
                <h3>
                    @lang('rooms.room') No.:
                </h3>
                <p>
                    {{ $room->number }}

                    @if ($room->is_suite)
                        <i class="fas fa-star"></i>
                    @endif
                </p>
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-md-3">
                <h3>Hotel:</h3>
                <p>{{ $room->hotel->business_name }}</p>
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-md-3">
                <h3>@lang('common.status'):</h3>
                <p>
                    @switch($room->status)
                        @case('0')
                            @lang('rooms.occupied')
                            @break
                        @case('1')
                            @lang('rooms.available')
                            @break
                        @case('2')
                            @lang('rooms.cleaning')
                            @break
                        @case('3')
                            @lang('rooms.disabled')
                            @break
                        @case('4')
                            @lang('rooms.maintenance')
                            @break
                    @endswitch
                </p>
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-md-3">
                <h3>@lang('common.floor'):</h3>
                <p>{{ $room->floor }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-3 col-sm-3 col-md-3 col-md-3">
                <h3>@lang('common.price'):</h3>
                <p>{{ number_format($room->price, 2, ',', '.') }}</p>
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-md-3">
                <h3>@lang('common.min_price'):</h3>
                <p>{{ number_format($room->min_price, 2, ',', '.') }}</p>
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-md-3">
                <h3>@lang('common.capacity')</h3>
                <p>{{ $room->capacity }}</p>
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-md-3">
                <h3>@lang('common.tax')</h3>
                <p>{{ number_format($room->tax * 100, 0, ',', '.') }}%</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h3>@lang('assets.title')</h3>

                @include('partials.list', [
                    'data' => $room->assets,
                    'listHeading' => 'app.assets.list-heading',
                    'listRow' => 'app.assets.list-row',
                    'where' => null,
                ])
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h3>@lang('products.title')</h3>

                @include('partials.list', [
                    'data' => $room->assets,
                    'listHeading' => 'app.assets.list-heading',
                    'listRow' => 'app.assets.list-row',
                    'where' => null,
                ])
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection
