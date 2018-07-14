@extends('layouts.app')

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('common.idTypes'),
            'url' => route('identifications.index'),
            'options' => []
        ])

        <div class="row">
            <div class="col-md-12">
                @include('partials.list', [
                    'data' => $identifications,
                    'listHeading' => 'app.identification_types.list-heading',
                    'listRow' => 'app.identification_types.list-row',
                    'where' => null
                ])
            </div>
        </div>
    </div>

@endsection