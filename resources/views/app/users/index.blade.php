@extends('layouts.panel')

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('users.title'),
            'url' => route('users.index'),
            'options' => []
        ])

        <div class="row">
            <div class="col-md-12">
                @include('partials.tab-list', [
                    'data' => $users,
                    'listHeading' => 'app.users.list-heading',
                    'listRow' => 'app.users.list-row',
                    'tabs' => [
                        [
                            'id' => ucfirst(trans('common.all')),
                            'title' => trans('common.all')
                        ],
                        [
                            'id' => ucfirst(trans('common.actives')),
                            'title' => 'Activos',
                            'where' => [
                                'field' => 'status',
                                'values' => [1]
                            ]
                        ],
                        [
                            'id' => ucfirst(trans('common.inactives')),
                            'title' => 'Inactivos',
                            'where' => [
                                'field' => 'status',
                                'values' => [0]
                            ]
                        ]
                    ]
                ])
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection
