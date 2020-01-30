@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('shifts') }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => 'Hoteles',
            'url' => route('shifts.index'),
            'options' => []
        ])

        <div class="row">
            <div class="col-md-12">
                @include('partials.list', [
                    'data' => $shifts,
                    'listHeading' => 'app.shifts.list-heading',
                    'listRow' => 'app.shifts.list-row',
                    'where' => null
                ])
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection
