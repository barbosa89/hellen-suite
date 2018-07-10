@extends('layouts.app')

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('services.title'),
            'url' => route('services.index'),
            'options' => [
                [
                    'option' => trans('common.new'),
                    'url' => route('services.create')
                ],
            ]
        ])

        <div class="row">
            <div class="col-md-12">
                @include('partials.list', [
                    'data' => $services,
                    'listHeading' => 'app.services.list-heading',
                    'listRow' => 'app.services.list-row',
                    'where' => null
                ])
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection