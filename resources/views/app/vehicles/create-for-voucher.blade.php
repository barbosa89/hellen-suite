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
                    'option' => trans('common.register') . ' ' . trans('vehicles.vehicle'),
                    'url' => route('vouchers.vehicles.search', ['id' => id_encode($voucher->id)]),
                ],
                [
                    'option' => trans('vouchers.back'),
                    'url' => route('vouchers.show', [
                        'id' => id_encode($voucher->id)
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
                    'url' => route('vouchers.vehicles.store', [
                        'id' => id_encode($voucher->id)
                    ]),
                    'fields' => [
                        'app.vouchers.vehicles.create-fields',
                        'app.vehicles.create-fields',
                    ],
                    'btn' => trans('common.create'),
                    'link' => [
                        'href' => route('vouchers.show', ['id' => id_encode($voucher->id)]),
                        'name' => trans('common.back')
                    ]
                ])
            </div>
        </div>

        @include('partials.spacer', ['size' => 'md'])
    </div>

@endsection