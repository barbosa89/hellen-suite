@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('assets') }}
@endsection


@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('assets.title'),
            'url' => route('assets.index'),
            'options' => [
                [
                    'option' => trans('reports.title'),
                    'url' => route('assets.report')
                ],
                [
                    'option' => trans('common.new'),
                    'url' => route('assets.create')
                ],
            ]
        ])

        <asset-list :hotels="{{ $hotels->toJson() }}"></asset-list>

        @include('partials.modal-confirm')
    </div>

@endsection