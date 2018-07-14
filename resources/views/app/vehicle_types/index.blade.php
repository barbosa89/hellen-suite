@extends('layouts.app')

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('common.vehicleTypes'),
            'url' => route('vehicles.index'),
            'options' => []
        ])

        <div class="row">
            <div class="col-md-12">
                @include('partials.list', [
                    'data' => $vehicles,
                    'listHeading' => 'app.vehicle_types.list-heading',
                    'listRow' => 'app.vehicle_types.list-row',
                    'where' => null
                ])
            </div>
        </div>
    </div>

@endsection