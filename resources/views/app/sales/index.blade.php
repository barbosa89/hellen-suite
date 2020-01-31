@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('sales', $product) }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('sales.title'),
            'url' => route('sales.index'),
            'options' => [
                [
                    'option' => trans('common.new'),
                    'url' => route('sales.create'),
                    'permission' => 'sales.create'
                ],
            ]
        ])

        <div class="row">
            <div class="col-md-12">
                @include('partials.list', [
                    'data' => $product->sales,
                    'listHeading' => 'app.sales.list-heading',
                    'listRow' => 'app.sales.list-row',
                    'where' => null
                ])
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection
