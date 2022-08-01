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
                    'option' => trans('reports.list'),
                    'url' => route('assets.export.form'),
                    'permission' => 'assets.create'
                ],
                [
                    'option' => trans('common.new'),
                    'url' => route('assets.create'),
                    'permission' => 'assets.create'
                ],
            ]
        ])

        <asset-list :hotels="{{ $hotels->toJson() }}"></asset-list>

        @include('partials.modal-confirm')
    </div>

@endsection
