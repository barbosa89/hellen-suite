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

        <div class="row">
            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                <h3>@lang('team.member'):</h3>
                <p>{{ auth()->user()->name }}</p>
            </div>
            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                <h3>@lang('common.created.at'):</h3>
                {{ $shift->created_at->format('Y-m-d H:m') }}
            </div>
            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                <h3>@lang('common.closed.at'):</h3>
                {{ $shift->closed_at ? $shift->closed_at->format('Y-m-d H:m') : '' }}
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                <h3>@lang('payments.cash'):</h3>
                {{ number_format($shift->cash, 2, ',', '.') }}
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                <h3>Hotel:</h3>
                <p>{{ $shift->hotel->business_name }}</p>
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
                            'id' => ucfirst(trans('common.all')),
                            'title' => trans('common.all')
                        ],
                        [
                            'id' => ucfirst(trans('payments.cash')),
                            'title' => trans('payments.cash'),
                            'data' => $shift->vouchers->where('payments.payment_method', 'cash')
                        ],
                        [
                            'id' => ucfirst(trans('payments.transfer')),
                            'title' => trans('payments.transfer'),
                            'data' => $shift->vouchers->where('payments.payment_method', 'transfer')
                        ],
                        [
                            'id' => ucfirst(trans('payments.courtesy')),
                            'title' => trans('payments.courtesy'),
                            'data' => $shift->vouchers->where('payments.payment_method', 'courtesy')
                        ]
                    ]
                ])
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection
