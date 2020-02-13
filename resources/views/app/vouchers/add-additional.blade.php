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
            'title' => trans('vouchers.addAdditional'),
            'align' => 'text-center',
            'size' => 'h3'
        ],
        'url' => route('invoices.additionals.store', [
            'id' => Hashids::encode($voucher->id)
        ]),
        'fields' => [
            'app.invoices.additionals.create-fields',
        ],
        'btn' => trans('common.add'),
        'link' => [
            'href' => route('invoices.show', ['id' => Hashids::encode($voucher->id)]),
            'name' => trans('common.back')
        ]
    ])

    @include('partials.spacer', ['size' => 'xs'])
@endsection