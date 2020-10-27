@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('plans') }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('plans.title'),
            'url' => route('plans.index'),
            'options' => []
        ])

        <div class="row">
            <div class="col-md-12">
                @include('partials.list', [
                    'data' => $plans,
                    'listHeading' => 'app.plans.list-heading',
                    'listRow' => 'app.plans.list-row',
                    'where' => null
                ])
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection
