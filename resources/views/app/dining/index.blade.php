@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('dining') }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('dining.title'),
            'url' => route('dining.index'),
            'options' => [
                [
                    'option' => trans('dining.new.item'),
                    'url' => route('dining.create')
                ],
            ]
        ])

        <dining-service-list :hotels="{{ $hotels->toJson() }}"></dining-service-list>

        @include('partials.modal-confirm')
    </div>

@endsection