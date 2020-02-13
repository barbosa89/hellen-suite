@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('invoice', $voucher) }}
@endsection

@section('content')

    @include('partials.page-header', [
        'title' => trans('vouchers.title'),
        'url' => route('invoices.index'),
        'options' => [
            [
                'option' => 'Buscar huéspedes',
                'url' => route('invoices.guests.search', ['id' => Hashids::encode($voucher->id)])
            ],
            [
                'option' => $voucher->company ? trans('vouchers.linkNewCompany') : trans('vouchers.linkCompany'),
                'url' => route('invoices.companies.search', [
                    'id' => Hashids::encode($voucher->id)
                ]),
            ],
            [
                'option' => 'Volver al recibo',
                'url' => route('invoices.show', [
                    'id' => Hashids::encode($voucher->id)
                ])
            ]
        ]
    ])

    @include('app.invoices.info')

    @include('partials.spacer', ['size' => 'xs'])

    @include('partials.form', [
        'title' => [
            'title' => trans('guests.guest'),
        ],
        'url' => route('invoices.guests.add', ['id' => Hashids::encode($voucher->id)]),
        'fields' => [
            'app.invoices.guests.info',
            'app.invoices.guests.create-fields'
        ],
        'btn' => trans('common.register')
    ])

    @include('partials.spacer', ['size' => 'md'])

@endsection
