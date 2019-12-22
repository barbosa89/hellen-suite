@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('process') }}
@endsection

@section('content')

    @include('partials.page-header', [
        'title' => trans('invoices.title'),
        'url' => route('invoices.index'),
        'search' => [
            'action' => route('invoices.search')
        ],
        'options' => [
            [
                'type' => 'confirm',
                'option' => trans('invoices.process'),
                'url' => route('invoices.process'),
                'method' => 'POST'
            ],
            [
                'option' => trans('common.back'),
                'url' => route('invoices.index')
            ],
        ]
    ])

    {{-- <div class="row">
        <div class="col-md-12">
            @include('partials.list', [
                'data' => $invoices,
                'listHeading' => 'app.invoices.list-heading',
                'listRow' => 'app.invoices.list-row',
            ])
        </div>
    </div> --}}
    <div id="page-wrapper">
        <process-list :invoices="{{ $invoices->toJson() }}"></process-list>

        @include('partials.modal-confirm')
    </div>

    @include('partials.modal-confirm')

@endsection