@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('product', $product) }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('products.title'),
            'url' => route('products.index'),
            'options' => [
                [
                    'type' => 'dropdown',
                    'option' => trans('common.options'),
                    'url' => [
                        [
                            'option' => trans('common.edit'),
                            'url' => route('products.edit', [
                                'id' => Hashids::encode($product->id)
                            ]),
                        ],
                        [
                            'option' => $product->status ? trans('common.disable') : trans('common.enable'),
                            'url' => route('products.toggle', [
                                'id' => Hashids::encode($product->id)
                            ])
                        ],
                        [
                            'type' => 'confirm',
                            'option' => trans('common.delete.item'),
                            'url' => route('products.destroy', [
                                'id' => Hashids::encode($product->id)
                            ]),
                            'method' => 'DELETE'
                        ],
                    ]
                ],
                [
                    'option' => trans('transactions.title'),
                    'url' => route('products.transactions'),
                ],
                [
                    'option' => trans('common.new'),
                    'url' => route('products.create')
                ],
                [
                    'option' => trans('common.back'),
                    'url' => route('products.index')
                ],
            ]
        ])

        @include('app.products.info')

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="transactions-tab" data-toggle="tab" href="#transactions" role="tab" aria-controls="transactions" aria-selected="true">
                    @lang('transactions.title')
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="charts-tab" data-toggle="tab" href="#charts" role="tab" aria-controls="charts" aria-selected="false">
                    @lang('common.chart')
                </a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="transactions" role="tabpanel" aria-labelledby="transactions-tab">
                @include('partials.list', [
                    'data' => $product->transactions,
                    'listHeading' => 'app.products.transactions.list-heading',
                    'listRow' => 'app.products.transactions.list-row'
                ])
            </div>
            <div class="tab-pane fade" id="charts" role="tabpanel" aria-labelledby="charts-tab">
                <canvas id="myChart"></canvas>
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection
