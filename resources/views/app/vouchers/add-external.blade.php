@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('voucher', $voucher) }}
@endsection

@section('content')

    @include('partials.page-header', [
        'title' => trans('vouchers.title'),
        'url' => route('vouchers.index'),
        'options' => [
            [
                'option' => trans('vouchers.back'),
                'url' => route('vouchers.show', [
                    'id' => id_encode($voucher->id)
                ])
            ]
        ]
    ])

    @include('app.vouchers.info')

    @include('partials.spacer', ['size' => 'xs'])

    @include('partials.form', [
        'title' => [
            'title' => trans('vouchers.external'),
            'align' => 'text-center',
            'size' => 'h3'
        ],
        'url' => route('vouchers.external.store', [
            'id' => id_encode($voucher->id)
        ]),
        'fields' => [
            'app.vouchers.additionals.create-fields',
        ],
        'btn' => trans('common.add'),
        'link' => [
            'href' => route('vouchers.show', ['id' => id_encode($voucher->id)]),
            'name' => trans('common.back')
        ]
    ])

    @include('partials.spacer', ['size' => 'xs'])
@endsection
