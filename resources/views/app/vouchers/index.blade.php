@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('invoices') }}
@endsection

@section('content')

    @include('partials.page-header', [
        'title' => trans('vouchers.title'),
        'url' => route('invoices.index'),
        'search' => [
            'action' => route('invoices.search')
        ],
        'options' => [
            [
                'type' => 'hideable',
                'option' => trans('vouchers.process'),
                'url' => route('invoices.process.form'),
                'show' => $vouchers->isNotEmpty(),
                'permission' => 'invoices.edit'
            ],
            [
                'option' => trans('common.new'),
                'url' => route('rooms.index'),
                'permission' => 'rooms.index'
            ],
        ]
    ])

    <div class="row">
        <div class="col-md-12">
            @include('partials.tab-list', [
                'data' => $vouchers,
                'listHeading' => 'app.invoices.list-heading',
                'listRow' => 'app.invoices.list-row',
                'tabs' => [
                    [
                        'id' => 'all',
                        'title' => trans('common.all'),
                    ],
                    [
                        'id' => 'open',
                        'title' => trans('vouchers.open'),
                        'data' => $vouchers->where('open', true),
                        'type' => 'check',
                        'form-id' => 'close-invoices',
                        'action' => '#'
                    ],
                    [
                        'id' => 'closed',
                        'title' => trans('vouchers.closed'),
                        'data' => $vouchers->where('open', false),
                    ],
                    [
                        'id' => 'unpaid',
                        'title' => trans('payments.unpaid'),
                        'data' => $vouchers->where('payment_status', false),
                    ]
                ]
            ])
        </div>
    </div>

    @include('partials.modal-confirm')

@endsection