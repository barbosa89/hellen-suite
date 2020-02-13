@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('vouchers') }}
@endsection

@section('content')

    @include('partials.page-header', [
        'title' => trans('vouchers.title'),
        'url' => route('vouchers.index'),
        'search' => [
            'action' => route('vouchers.search')
        ],
        'options' => [
            [
                'type' => 'hideable',
                'option' => trans('vouchers.process'),
                'url' => route('vouchers.process.form'),
                'show' => $vouchers->isNotEmpty(),
                'permission' => 'vouchers.edit'
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
                'listHeading' => 'app.vouchers.list-heading',
                'listRow' => 'app.vouchers.list-row',
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
                        'form-id' => 'close-vouchers',
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