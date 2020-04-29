@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('shift', $shift) }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('shifts.title'),
            'url' => route('shifts.index'),
            'options' => [
                [
                    'option' => trans('common.back'),
                    'url' => route('shifts.index')
                ],
            ]
        ])

        <div class="row my-4">
            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                <span>@lang('team.member'):</span>
                <h5>{{ auth()->user()->name }}</h5>
            </div>
            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                <span>@lang('common.created.at'):</span>
                <h5>{{ $shift->created_at->format('Y-m-d H:m') }}</h5>
            </div>
            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                <span>@lang('common.closed.at'):</span>
                {{ $shift->closed_at ? $shift->closed_at->format('Y-m-d H:m') : '' }}
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                <span>@lang('payments.cash'):</span>
                <h5>{{ number_format($shift->cash, 2, ',', '.') }}</h5>
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                <span>Hotel:</span>
                <h5>{{ $shift->hotel->business_name }}</h5>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                @include('partials.tab-list', [
                    'data' => $shift->vouchers,
                    'listHeading' => 'app.shifts.vouchers.list-heading',
                    'listRow' => 'app.shifts.vouchers.list-row',
                    'tabs' => [
                        [
                            'id' => ucfirst(trans('vouchers.title')),
                            'title' => trans('vouchers.title')
                        ],
                        [
                            'id' => ucfirst(trans('payments.cash')),
                            'title' => trans('payments.cash'),
                            'data' => $cash
                        ],
                        [
                            'id' => ucfirst(trans('payments.transfer')),
                            'title' => trans('payments.transfer'),
                            'data' => $transfer
                        ],
                        [
                            'id' => ucfirst(trans('payments.courtesy')),
                            'title' => trans('payments.courtesy'),
                            'data' => $courtesy
                        ],
                        [
                            'id' => ucfirst(trans('vouchers.losses')),
                            'title' => trans('vouchers.losses'),
                            'data' => $shift->vouchers->where('type', 'loss')
                        ],
                        [
                            'id' => ucfirst(trans('rooms.title')),
                            'title' => trans('rooms.title'),
                            'listHeading' => 'app.shifts.rooms.list-heading',
                            'listRow' => 'app.shifts.rooms.list-row',
                            'data' => $rooms
                        ]
                    ]
                ])
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection
