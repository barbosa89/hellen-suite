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
                    'option' => trans('common.new'),
                    'url' => route('props.create')
                ],
                [
                    'option' => trans('common.back'),
                    'url' => route('props.index')
                ]
            ]
        ])

        <prop-transactions :hotels="{{ $hotels->toJson() }}"></prop-transactions>

        <div class="row">
            <div class="col-md-12">
                <div class="spacer-md"></div>
            </div>
        </div>
    </div>

@endsection