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
                'action' => route('vehicles.search')
            ],
            'options' => [
                [
                    'type' => 'hideable',
                    'option' => trans('reports.list'),
                    'url' => route('vehicles.export'),
                    'show' => $vehicles->isNotEmpty(),
                    'permission' => 'vehicles.index'
                ],
                [
                    'option' => trans('common.new'),
                    'url' => route('vehicles.create'),
                    'permission' => 'vehicles.create'
                ],
            ]
        ])

        <div class="row">
            <div class="col-md-12">
                @include('partials.list', [
                    'data' => $vehicles,
                    'listHeading' => 'app.vehicles.list-heading',
                    'listRow' => 'app.vehicles.list-row'
                ])
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection