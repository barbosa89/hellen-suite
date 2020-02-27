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
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                <h2>@lang('common.description'):</h2>
                <p>{{ auth()->user()->name }}</p>
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                <h2>@lang('common.quantity'):</h2>
                {{ $shift->created_at->format('Y-m-d') }}
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                <h2>@lang('common.value'):</h2>
                {{ number_format($shift->cash, 2, ',', '.') }}
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                <h3>@lang('common.model'):</h3>
                <p>{{ $shift->hotel->business_name }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                @include('partials.list', [
                    'data' => $shift->vouchers,
                    'listHeading' => 'app.shifts.vouchers.list-heading',
                    'listRow' => 'app.shifts.vouchers.list-row'
                ])
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection
