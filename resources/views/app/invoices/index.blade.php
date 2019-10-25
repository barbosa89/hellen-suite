@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('invoices') }}
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
                'option' => trans('common.new'),
                'url' => route('rooms.index')
            ],
        ]
    ])

    <div class="row">
        <div class="col-md-12">
            @include('partials.list', [
                'data' => $invoices,
                'listHeading' => 'app.invoices.list-heading',
                'listRow' => 'app.invoices.list-row',
                'where' => null
            ])
        </div>
    </div>

    @include('partials.modal-confirm')

@endsection