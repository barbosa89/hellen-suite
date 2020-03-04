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
                [
                    'option' => trans('common.back'),
                    'url' => route('rooms.index')
                ],
            ],
            'search' => [
                'action' => route('rooms.search'),
                'query' => $query
            ]
        ])

        <div class="row">
            <div class="col-md-12">
                @include('partials.list', [
                    'data' => $rooms,
                    'listHeading' => 'app.rooms.admin.list-heading',
                    'listRow' => 'app.rooms.admin.list-row',
                    'where' => null
                ])
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection