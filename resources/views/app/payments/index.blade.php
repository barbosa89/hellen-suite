@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('payments', $voucher) }}
@endsection

@section('content')

@include('partials.page-header', [
    'title' => trans('payments.title'),
    'url' => route('payments.index', [
        'invoice' => Hashids::encode($voucher->id)
    ]),
    'options' => [
        [
            'type' => $voucher->payment_status ? 'hideable' : 'confirm',
            'option' => trans('common.close') . ' ' . strtolower(trans('payments.title')),
            'url' => route('invoices.payments.close', [
                'id' => Hashids::encode($voucher->id)
            ]),
            'show' => (float) $voucher->value === $voucher->payments->sum('value') and $voucher->payment_status == false,
            'method' => 'POST',
            'permission' => 'invoices.payments.close'
        ],
        [
            'type' => 'hideable',
            'option' => trans('common.new'),
            'url' => route('payments.create', [
                'invoice' => Hashids::encode($voucher->id)
            ]),
            'show' => $voucher->payment_status == false,
            'permission' => 'payments.create'
        ],
    ]
])

@include('app.invoices.info')

@include('partials.spacer', ['size' => 'sm'])

<div class="row">
    <div class="col-md-12">
        @include('partials.list', [
            'data' => $voucher->payments,
            'listHeading' => 'app.payments.list-heading',
            'listRow' => 'app.payments.list-row',
            'where' => null
        ])
    </div>
</div>

@include('partials.modal-confirm')

@endsection