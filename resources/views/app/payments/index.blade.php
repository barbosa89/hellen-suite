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
            'type' => $invoice->payment_status ? 'hideable' : 'confirm',
            'option' => trans('common.close') . ' ' . strtolower(trans('payments.title')),
            'url' => route('invoices.payments.close', [
                'id' => Hashids::encode($invoice->id)
            ]),
            'show' => (float) $invoice->value === $invoice->payments->sum('value') and $invoice->payment_status == false,
            'method' => 'POST'
        ],
        [
            'type' => 'hideable',
            'option' => trans('common.new'),
            'url' => route('payments.create', [
                'invoice' => Hashids::encode($invoice->id)
            ]),
            'show' => $invoice->payment_status == false
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