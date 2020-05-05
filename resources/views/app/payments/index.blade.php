@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('payments', $voucher) }}
@endsection

@section('content')

    @include('partials.page-header', [
        'title' => trans('payments.title'),
        'url' => route('payments.index', [
            'voucher' => id_encode($voucher->id)
        ]),
        'options' => [
            [
                'type' => (float) $voucher->value == (float) $voucher->payments->sum('value') ? 'confirm' : 'hideable',
                'option' => trans('common.close') . ' ' . strtolower(trans('payments.title')),
                'url' => route('vouchers.payments.close', [
                    'id' => id_encode($voucher->id)
                ]),
                'show' => (float) $voucher->value == (float) $voucher->payments->sum('value') and !$voucher->payment_status,
                'method' => 'POST',
                'permission' => 'payments.close'
            ],
            [
                'type' => 'hideable',
                'option' => trans('common.new'),
                'url' => route('payments.create', [
                    'voucher' => id_encode($voucher->id)
                ]),
                'show' => $voucher->payment_status == false,
                'permission' => 'payments.create'
            ],
        ]
    ])

    @include('app.vouchers.info')

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