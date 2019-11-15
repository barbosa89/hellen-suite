@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('vehicles') }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('vehicles.title'),
            'url' => route('vehicles.index'),
            'search' => [
                'action' => route('vehicles.search'),
                'query' => $query
            ],
            'options' => [
                [
                    'option' => trans('common.new'),
                    'url' => route('vehicles.create')
                ],
                [
                    'option' => trans('common.back'),
                    'url' => route('vehicles.index')
                ]
            ]
        ])

        <div class="row">
            <div class="col-md-12">
                @include('partials.list', [
                    'data' => $vehicles,
                    'listHeading' => 'app.vehicles.list-heading',
                    'listRow' => 'app.vehicles.list-row',
                    'where' => null
                ])
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection