@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('voucher', $voucher) }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('vouchers.title'),
            'url' => route('vouchers.index'),
            'options' => [
                [
                    'option' => trans('common.new') . ' ' . strtolower(trans('guests.guest')),
                    'url' => route('vouchers.guests.create', ['id' => id_encode($voucher->id)])
                ],
                [
                    'option' => trans('vouchers.linkCompany'),
                    'url' => route('vouchers.companies.search', [
                        'id' => id_encode($voucher->id)
                    ])
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

        <h1 class="text-center mb-4">@lang('common.search') @lang('guests.title')</h1>

        <search-guests :voucher-hash='@json($voucher->hash)'></search-guests>
    </div>

@endsection
