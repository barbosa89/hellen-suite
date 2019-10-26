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
                    'option' => trans('common.new'),
                    'url' => route('assets.create')
                ],
            ]
        ])

        <div class="row">
            <div class="col-md-12">
                @include('partials.list', [
                    'data' => $assets,
                    'listHeading' => 'app.assets.list-heading',
                    'listRow' => 'app.assets.list-row',
                    'where' => null
                ])
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection