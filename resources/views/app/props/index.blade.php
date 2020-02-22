@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('props') }}
@endsection


@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('props.title'),
            'url' => route('props.index'),
            'options' => [
                [
                    'option' => trans('common.report'),
                    'url' => route('props.report')
                ],
                [
                    'option' => trans('transactions.title'),
                    'url' => route('props.vouchers.create')
                ],
                [
                    'option' => trans('common.new'),
                    'url' => route('props.create')
                ],
            ]
        ])

        <prop-list :hotels="{{ $hotels->toJson() }}"></prop-list>

        @include('partials.modal-confirm')
    </div>

@endsection