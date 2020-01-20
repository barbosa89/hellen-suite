@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('guests') }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('guests.title'),
            'url' => route('guests.index'),
            'search' => [
                'action' => route('guests.search'),
                'query' => $query
            ],
            'options' => [
                [
                    'option' => trans('common.new'),
                    'url' => route('guests.create'),
                    'permission' => 'guests.create'
                ],
                [
                    'option' => trans('common.back'),
                    'url' => route('guests.index'),
                    'permission' => 'guests.index'
                ],
            ]
        ])

        <div class="row">
            <div class="col-md-12">
                @include('partials.list', [
                    'data' => $guests,
                    'listHeading' => 'app.guests.list-heading',
                    'listRow' => 'app.guests.list-row'
                ])
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection