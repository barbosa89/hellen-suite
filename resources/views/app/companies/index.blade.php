@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('companies') }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('companies.title'),
            'url' => route('companies.index'),
            'search' => [
                'action' => route('companies.search')
            ],
            'options' => [
                [
                    'option' => trans('reports.list'),
                    'url' => route('companies.export')
                ],
                [
                    'option' => trans('common.new'),
                    'url' => route('companies.create')
                ],
            ]
        ])

        <div class="row">
            <div class="col-md-12">
                @include('partials.list', [
                    'data' => $companies,
                    'listHeading' => 'app.companies.list-heading',
                    'listRow' => 'app.companies.list-row',
                    'where' => null
                ])
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection