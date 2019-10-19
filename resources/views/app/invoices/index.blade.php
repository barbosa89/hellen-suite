@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('invoices') }}
@endsection

@section('content')

    @include('partials.page-header', [
        'title' => trans('invoices.title'),
        'url' => route('invoices.index'),
        'search' => [
            'action' => '#'
        ],
        'options' => [
            [
                'option' => trans('common.new'),
                'url' => route('invoices.create')
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

    @include('partials.modal-form', [
        'title' => trans('common.creationOf') . ' ' . trans('invoices.title'),
        'id' => 'new-invoice',
        'action' => route('invoices.store'),
        'fields' => 'app.invoices.new'
    ])

@endsection