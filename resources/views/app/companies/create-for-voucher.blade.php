@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('voucher', $voucher) }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('companies.title'),
            'url' => route('companies.index'),
            'options' => [
                [
                    'option' => 'Buscar empresas',
                    'url' => route('vouchers.companies.search', ['id' => Hashids::encode($voucher->id)])
                ],
                [
                    'option' => 'Volver al recibo',
                    'url' => route('vouchers.show', [
                        'id' => Hashids::encode($voucher->id)
                    ])
                ]
            ]
        ])

        <div class="row">
            <div class="col-md-12">
                @include('partials.form', [
                    'title' => [
                        'title' => trans('common.creationOf') . ' ' . trans('companies.title'),
                        'align' => 'text-center'
                    ],
                    'url' => route('vouchers.companies.store', [
                        'id' => Hashids::encode($voucher->id)
                    ]),
                    'fields' => [
                        'app.companies.create-fields',
                    ],
                    'btn' => trans('common.create')
                ])
            </div>
        </div>

        @include('partials.spacer', ['size' => 'md'])
    </div>

@endsection
