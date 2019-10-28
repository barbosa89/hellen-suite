@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('services') }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('services.title'),
            'url' => route('services.index'),
            'options' => [
                [
                    'option' => trans('common.new'),
                    'url' => route('services.create')
                ],
            ]
        ])

        <service-list :hotels="{{ $hotels->toJson() }}"></service-list>

        @include('partials.modal-confirm')
    </div>

@endsection