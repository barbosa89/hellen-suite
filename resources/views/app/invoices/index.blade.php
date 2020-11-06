@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('invoices') }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('invoices.title'),
            'url' => route('invoices.index'),
            'options' => []
        ])

        <div class="row">
            <div class="col">
                @include('partials.table', [
                    'data' => $invoices,
                    'headers' => [
                        trans('common.number'),
                        trans('common.name'),
                        trans('common.idNumber'),
                        trans('common.total'),
                        trans('common.status'),
                        trans('common.date'),
                        trans('common.options')
                    ],
                    'row' => 'app.invoices.row'
                ])
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection
