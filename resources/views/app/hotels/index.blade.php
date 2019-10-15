@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('hotels') }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => 'Hoteles',
            'url' => route('hotels.index'),
            'options' => [
                [
                    'option' => trans('common.new'),
                    'url' => route('hotels.create')
                ],
            ]
        ])

        <div class="row">
            <div class="col-md-12">
                @include('partials.list', [
                    'data' => $hotels,
                    'listHeading' => 'app.hotels.list-heading',
                    'listRow' => 'app.hotels.list-row',
                    'where' => null
                ])
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection