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
                <h2>@lang('invoices.invoice') No. {{ $invoice->number }}</h2>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-md-6">
                <h2>@lang('common.value'): {{ number_format($invoice->value, 2, ',', '.') }}</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h3>@lang('rooms.title')</h3>

                @include('partials.check-list', [
                    'id' => 'attach-rooms',
                    'url' => route('invoices.attach.rooms', [
                        'id' => Hashids::encode($invoice->id)
                    ]),
                    'data' => $rooms,
                    'listHeading' => 'app.invoices.rooms.list-heading',
                    'listRow' => 'app.invoices.rooms.list-row',
                    'where' => null,
                ])
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection