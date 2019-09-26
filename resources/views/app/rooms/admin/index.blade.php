@extends('layouts.panel')

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('rooms.title'),
            'url' => route('rooms.index'),
            'options' => [
                [
                    'option' => trans('common.new'),
                    'url' => route('rooms.create')
                ],
            ],
            'search' => [
                'action' => route('rooms.search')
            ]
        ])

        <div class="row">
            <div class="col-md-12">
                @include('partials.tab-list', [
                    'data' => $rooms,
                    'listHeading' => 'app.rooms.admin.list-heading',
                    'listRow' => 'app.rooms.admin.list-row',
                    'tabs' => [
                        [
                            'id' => ucfirst(trans('common.all')),
                            'title' => trans('common.all')
                        ],
                        [
                            'id' => ucfirst(trans('rooms.available')),
                            'title' => trans('rooms.available'),
                            'listHeading' => 'app.rooms.admin.custom-list-heading',
                            'listRow' => 'app.rooms.admin.custom-list-row',
                            'where' => [
                                'field' => 'status',
                                'values' => [1]
                            ]
                        ],
                        [
                            'id' => ucfirst(trans('rooms.occupied')),
                            'title' => trans('rooms.occupied'),
                            'listHeading' => 'app.rooms.admin.custom-list-heading',
                            'listRow' => 'app.rooms.admin.custom-list-row',
                            'where' => [
                                'field' => 'status',
                                'values' => [0]
                            ]
                        ],
                        [
                            'id' => ucfirst(trans('rooms.cleaning')),
                            'title' => trans('rooms.cleaning'),
                            'listHeading' => 'app.rooms.admin.custom-list-heading',
                            'listRow' => 'app.rooms.admin.custom-list-row',
                            'where' => [
                                'field' => 'status',
                                'values' => [4]
                            ]
                        ],
                        [
                            'id' => ucfirst(trans('rooms.maintenance')),
                            'title' => trans('rooms.maintenance'),
                            'listHeading' => 'app.rooms.admin.custom-list-heading',
                            'listRow' => 'app.rooms.admin.custom-list-row',
                            'where' => [
                                'field' => 'status',
                                'values' => [2]
                            ]
                        ],
                        [
                            'id' => ucfirst(trans('rooms.disabled')),
                            'title' => trans('rooms.disabled'),
                            'listHeading' => 'app.rooms.admin.custom-list-heading',
                            'listRow' => 'app.rooms.admin.custom-list-row',
                            'where' => [
                                'field' => 'status',
                                'values' => [3]
                            ]
                        ],
                    ]
                ])
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection