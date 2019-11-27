@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('payments', $invoice) }}
@endsection

@section('content')

@include('partials.page-header', [
    'title' => trans('payments.title'),
    'url' => route('payments.index', [
        'invoice' => Hashids::encode($invoice->id)
    ]),
    'options' => [
        [
            'option' => trans('common.new'),
            'url' => route('payments.create', [
                'invoice' => Hashids::encode($invoice->id)
            ]),
        ],
    ]
])

@include('app.invoices.info')

@include('partials.spacer', ['size' => 'sm'])

<div class="row">
    <div class="col-md-12">
        @include('partials.list', [
            'data' => $invoice->payments,
            'listHeading' => 'app.payments.list-heading',
            'listRow' => 'app.payments.list-row',
            'where' => null
        ])
    </div>
</div>

@include('partials.modal-confirm')

@endsection