@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('company', $company) }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('companies.title'),
            'url' => route('companies.index'),
            'options' => [
                [
                    'type' => 'dropdown',
                    'option' => trans('common.options'),
                    'url' => [
                        [
                            'option' => trans('common.edit'),
                            'url' => route('companies.edit', [
                                'id' => Hashids::encode($company->id)
                            ]),
                        ],
                        [
                            'type' => 'confirm',
                            'option' => trans('common.delete.item'),
                            'url' => route('companies.destroy', [
                                'id' => Hashids::encode($company->id)
                            ]),
                            'method' => 'DELETE'
                        ],
                    ]
                ],
                [
                    'option' => trans('common.new'),
                    'url' => route('companies.create')
                ],
                [
                    'option' => trans('common.back'),
                    'url' => route('companies.index')
                ],
            ]
        ])

        @include('app.companies.info')

        <div class="row">
            <div class="col-md-12">
                @include('partials.list', [
                    'data' => $vouchers->take(20),
                    'listHeading' => 'app.companies.vouchers.list-heading',
                    'listRow' => 'app.companies.vouchers.list-row'
                ])
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
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
