@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('invoice', $voucher) }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('companies.title'),
            'url' => route('companies.index'),
            'options' => [
                [
                    'option' => trans('common.register') . ' ' . trans('vehicles.vehicle'),
                    'url' => route('invoices.vehicles.search', ['id' => Hashids::encode($voucher->id)]),
                ],
                [
                    'option' => 'Volver al recibo',
                    'url' => route('invoices.show', [
                        'id' => Hashids::encode($voucher->id)
                    ])
                ]
            ]
        ])

        <div class="row">
            <div class="col-md-12">
                @include('partials.form', [
                    'title' => [
                        'title' => trans('common.creationOf') . ' ' . trans('vehicles.title'),
                        'align' => 'text-center'
                    ],
                    'url' => route('invoices.vehicles.store', [
                        'id' => Hashids::encode($voucher->id)
                    ]),
                    'fields' => [
                        'app.invoices.vehicles.create-fields',
                        'app.vehicles.create-fields',
                    ],
                    'btn' => trans('common.create'),
                    'link' => [
                        'href' => route('invoices.show', ['id' => Hashids::encode($voucher->id)]),
                        'name' => trans('common.back')
                    ]
                ])
            </div>
        </div>

        @include('partials.spacer', ['size' => 'md'])
    </div>

@endsection