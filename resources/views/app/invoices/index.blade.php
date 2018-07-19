@extends('layouts.app')

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('invoices.title'),
            'url' => route('invoices.index'),
            'options' => [
                [
                    'type' => 'post',
                    'option' => trans('common.new'),
                    'url' => route('invoices.store')
                ],
            ]
        ])

        <div class="row">
            <div class="col-md-12">
                @include('partials.list', [
                    'data' => $invoices,
                    'listHeading' => 'app.invoices.list-heading',
                    'listRow' => 'app.invoices.list-row',
                    'where' => null
                ])
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection