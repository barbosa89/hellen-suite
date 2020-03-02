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
                    'option' => trans('common.reports'),
                    'url' => route('products.product.report', [
                        'id' => Hashids::encode($product->id)
                    ])
                ],
                [
                    'option' => 'Transacciones',
                    'url' => route('products.vouchers.create')
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
                <a class="nav-link active" id="vouchers-tab" data-toggle="tab" href="#vouchers" role="tab" aria-controls="vouchers" aria-selected="true">
                    @lang('vouchers.title')
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="charts-tab" data-toggle="tab" href="#charts" role="tab" aria-controls="charts" aria-selected="false">
                    @lang('common.chart')
                </a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="vouchers" role="tabpanel" aria-labelledby="vouchers-tab">
                @include('partials.list', [
                    'data' => $product->vouchers,
                    'listHeading' => 'app.products.vouchers.list-heading',
                    'listRow' => 'app.products.vouchers.list-row'
                ])
            </div>
            <div class="tab-pane fade" id="charts" role="tabpanel" aria-labelledby="charts-tab">
                <canvas id="myChart"></canvas>
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection

@section('scripts')
    <script type="text/javascript">
        var ctx = document.getElementById('myChart');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [
                    translator.trans('months.january'),
                    translator.trans('months.february'),
                    translator.trans('months.march'),
                    translator.trans('months.april'),
                    translator.trans('months.may'),
                    translator.trans('months.june'),
                    translator.trans('months.july'),
                    translator.trans('months.august'),
                    translator.trans('months.september'),
                    translator.trans('months.october'),
                    translator.trans('months.november'),
                    translator.trans('months.december')
                ],
                datasets: Array.from({!! $data->toJson() !!})
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    </script>
@endsection
