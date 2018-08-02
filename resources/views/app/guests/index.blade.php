@extends('layouts.app')

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('guests.title'),
            'url' => route('guests.index'),
            'options' => [
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
                            'title' => trans('common.actives'),
                            'where' => [
                                'field' => 'status',
                                'values' => [1]
                            ]
                        ],
                        [
                            'id' => ucfirst(trans('common.inactives')),
                            'title' => trans('common.inactives'),
                            'where' => [
                                'field' => 'status',
                                'values' => [0]
                            ]
                        ],
                    ]
                ])
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection