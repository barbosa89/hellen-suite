@extends('layouts.app')

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
            ]
        ])

        <div class="row">
            <div class="col-md-12">
                @include('partials.tab-list', [
                    'data' => $rooms,
                    'listHeading' => 'app.rooms.list-heading',
                    'listRow' => 'app.rooms.list-row',
                    'tabs' => [
                        [
                            'id' => ucfirst(trans('common.all')),
                            'title' => trans('common.all')
                        ],
                        [
                            'id' => ucfirst(trans('rooms.free')),
                            'title' => trans('rooms.free'),
                            'where' => [
                                'field' => 'status',
                                'values' => [1]
                            ]
                        ],
                        [
                            'id' => ucfirst(trans('rooms.occupied')),
                            'title' => trans('rooms.occupied'),
                            'where' => [
                                'field' => 'status',
                                'values' => [0]
                            ]
                        ],
                        [
                            'id' => ucfirst(trans('rooms.maintenance')),
                            'title' => trans('rooms.maintenance'),
                            'where' => [
                                'field' => 'status',
                                'values' => [2]
                            ]
                        ],
                        [
                            'id' => ucfirst(trans('rooms.disabled')),
                            'title' => trans('rooms.disabled'),
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