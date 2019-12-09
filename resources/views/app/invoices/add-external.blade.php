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
            'title' => trans('invoices.external'),
            'align' => 'text-center',
            'size' => 'h3'
        ],
        'url' => route('invoices.external.store', [
            'id' => Hashids::encode($invoice->id)
        ]),
        'fields' => [
            'app.invoices.additionals.create-fields',
        ],
        'btn' => trans('common.add'),
        'link' => [
            'href' => route('invoices.show', ['id' => Hashids::encode($invoice->id)]),
            'name' => trans('common.back')
        ]
    ])

    @include('partials.spacer', ['size' => 'xs'])
@endsection