@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('team') }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('users.team'),
            'url' => route('team.index'),
            'options' => [
                [
                    'option' => trans('common.new'),
                    'url' => route('team.create'),
                    'permission' => 'team.create'
                ],
            ]
        ])

        <div class="row">
            <div class="col-md-12">
                @include('partials.list', [
                    'data' => $team,
                    'listHeading' => 'app.team.list-heading',
                    'listRow' => 'app.team.list-row',
                    'where' => null
                ])
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection