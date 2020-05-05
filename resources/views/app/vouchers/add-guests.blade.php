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
                'option' => trans('common.search') . ' ' . strtolower(trans('guests.title')),
                'url' => route('vouchers.guests.search', ['id' => id_encode($voucher->id)])
            ],
            [
                'option' => $voucher->company ? trans('vouchers.linkNewCompany') : trans('vouchers.linkCompany'),
                'url' => route('vouchers.companies.search', [
                    'id' => id_encode($voucher->id)
                ]),
            ],
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
            'title' => trans('guests.guest'),
        ],
        'url' => route('vouchers.guests.add', ['id' => id_encode($voucher->id)]),
        'fields' => [
            'app.vouchers.guests.info',
            'app.vouchers.guests.create-fields'
        ],
        'btn' => trans('common.register')
    ])

    @include('partials.spacer', ['size' => 'md'])

@endsection
