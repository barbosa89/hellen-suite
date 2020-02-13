@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('vouchers') }}
@endsection

@section('content')

    @include('partials.page-header', [
        'title' => trans('vouchers.title'),
        'url' => route('vouchers.index'),
        'search' => [
            'action' => route('vouchers.search'),
            'query' => $query
        ],
        'options' => [
            [
                'option' => trans('common.new'),
                'url' => route('rooms.index')
            ],
            [
                'option' => trans('common.back'),
                'url' => route('vouchers.index')
            ],
        ]
    ])

    <div class="row">
        <div class="col-md-12">
            @include('partials.list', [
                'data' => $vouchers,
                'listHeading' => 'app.vouchers.list-heading',
                'listRow' => 'app.vouchers.list-row'
            ])
        </div>
    </div>

    @include('partials.modal-confirm')

@endsection