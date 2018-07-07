@extends('layouts.app')

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('users.title'),
            'url' => route('receptionists.index'),
            'options' => [
                [
                    'option' => trans('common.new'),
                    'url' => route('receptionists.create')
                ],
            ]
        ])

        <div class="row">
            <div class="col-md-12">
                @include('partials.list', [
                    'data' => $users,
                    'listHeading' => 'app.receptionists.list-heading',
                    'listRow' => 'app.receptionists.list-row',
                    'where' => null
                ])
            </div>
        </div>
    </div>

    @include('partials.modal-confirm')

@endsection