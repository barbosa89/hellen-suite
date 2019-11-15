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
                'action' => route('guests.search')
            ],
            'options' => [
                [
                    'type' => 'hideable',
                    'option' => trans('reports.list'),
                    'url' => route('guests.export'),
                    'show' => $guests->isNotEmpty()
                ],
                [
                    'option' => trans('common.new'),
                    'url' => route('guests.create')
                ],
            ]
        ])

        <div class="row">
            <div class="col-md-12">
                @include('partials.tab-list', [
                    'data' => $guests,
                    'listHeading' => 'app.guests.list-heading',
                    'listRow' => 'app.guests.list-row',
                    'tabs' => [
                        [
                            'id' => ucfirst(trans('common.all')),
                            'title' => trans('common.all')
                        ],
                        [
                            'id' => ucfirst(trans('common.actives')),
                            'title' => 'Hospedados',
                            'where' => [
                                'field' => 'status',
                                'values' => [1]
                            ]
                        ]
                    ]
                ])
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection