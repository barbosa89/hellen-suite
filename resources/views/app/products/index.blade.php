@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('products') }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('products.title'),
            'url' => route('products.index'),
            'options' => [
                [
                    'option' => trans('common.reports'),
                    'url' => '#'
                ],
                [
                    'option' => trans('vouchers.title'),
                    'url' => route('products.vouchers.create')
                ],
                [
                    'option' => trans('common.new'),
                    'url' => route('products.create')
                ],
            ]
        ])

        <product-list :hotels="{{ $hotels->toJson() }}"></product-list>

        @include('partials.modal-confirm')
    </div>

@endsection