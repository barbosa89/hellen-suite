@extends('layouts.app')

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('invoices.title'),
            'url' => route('invoices.index'),
            'options' => [
                [
                    'option' => trans('common.options'),
                    'url' => '#'
                ],
                [
                    'option' => trans('common.back'),
                    'url' => url()->previous()
                ],
            ]
        ])

        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6 col-md-6">
                <h2>@lang('invoices.room') No. {{ $room->number }}</h2>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-md-6">
                <h2>@lang('common.value'):</h2>
                <p>{{ number_format($room->value, 2, ',', '.') }}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-6 col-md-6">
                <h3>@lang('common.description')</h3>
                <p>{{ $room->description }}</p>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-md-6">
                <h3>@lang('common.status')</h3>
                <p>@include('partials.room-status', ['status' => $room->status])</p>
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