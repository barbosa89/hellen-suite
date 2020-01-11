@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('invoices') }}
@endsection

@section('content')

    @include('partials.page-header', [
        'title' => trans('invoices.title'),
        'url' => route('invoices.index'),
        'search' => [
            'action' => route('invoices.search')
        ],
        'options' => [
            [
                'type' => 'hideable',
                'option' => trans('invoices.process'),
                'url' => route('invoices.process.form'),
                'show' => $invoices->isNotEmpty()
            ],
            [
                'option' => trans('common.new'),
                'url' => route('rooms.index')
            ],
        ]
    ])

    <div class="row">
        <div class="col-md-12">
            @include('partials.tab-list', [
                'data' => $invoices,
                'listHeading' => 'app.invoices.list-heading',
                'listRow' => 'app.invoices.list-row',
                'tabs' => [
                    [
                        'id' => 'all',
                        'title' => trans('common.all'),
                    ],
                    [
                        'id' => 'open',
                        'title' => trans('invoices.open'),
                        'data' => $invoices->where('open', true),
                        'type' => 'check',
                        'form-id' => 'close-invoices',
                        'action' => '#'
                    ],
                    [
                        'id' => 'closed',
                        'title' => trans('invoices.closed'),
                        'data' => $invoices->where('open', false),
                    ],
                    [
                        'id' => 'unpaid',
                        'title' => trans('payments.unpaid'),
                        'data' => $invoices->where('payment_status', false),
                    ]
                ]
            ])
        </div>
    </div>

    @include('partials.modal-confirm')

@endsection