@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('invoice', $invoice) }}
@endsection

@section('content')

    @include('partials.page-header', [
        'title' => trans('invoices.title'),
        'url' => route('invoices.index'),
        'options' => [
            [
                'option' => 'Buscar huéspedes',
                'url' => route('invoices.guests.search', ['id' => Hashids::encode($invoice->id)])
            ],
            [
                'option' => $invoice->company ? trans('invoices.linkNewCompany') : trans('invoices.linkCompany'),
                'url' => route('invoices.companies.search', [
                    'id' => Hashids::encode($invoice->id)
                ]),
            ],
            [
                'option' => 'Volver al recibo',
                'url' => route('invoices.show', [
                    'id' => Hashids::encode($invoice->id)
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
        'url' => route('invoices.guests.add', ['id' => Hashids::encode($invoice->id)]),
        'fields' => [
            'app.invoices.guests.info',
            'app.invoices.guests.create-fields'
        ],
        'btn' => trans('common.register')
    ])

    @include('partials.spacer', ['size' => 'md'])

@endsection
