@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('invoices') }}
@endsection

@section('content')

    @include('partials.page-header', [
        'title' => trans('invoices.title'),
        'url' => route('invoices.index'),
        'search' => [
            'action' => route('invoices.search'),
            'query' => $query
        ],
        'options' => [
            [
                'option' => trans('common.new'),
                'url' => route('rooms.index')
            ],
            [
                'option' => trans('common.back'),
                'url' => route('invoices.index')
            ],
        ]
    ])

    <div class="row">
        <div class="col-md-12">
            @include('partials.list', [
                'data' => $invoices,
                'listHeading' => 'app.invoices.list-heading',
                'listRow' => 'app.invoices.list-row'
            ])
        </div>
    </div>

    @include('partials.modal-confirm')

@endsection