@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('service', $service) }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('services.title'),
            'url' => route('services.index'),
            'options' => [
                [
                    'option' => trans('common.reports'),
                    'url' => route('services.service.report', [
                        'id' => Hashids::encode($service->id)
                    ])
                ],
                [
                    'option' => trans('common.new'),
                    'url' => route('services.create')
                ],
                [
                    'type' => 'dropdown',
                    'option' => trans('common.options'),
                    'url' => [
                        [
                            'option' => trans('common.edit'),
                            'url' => route('services.edit', [
                                'id' => Hashids::encode($service->id)
                            ]),
                        ],
                        [
                            'option' => $service->status ? trans('common.disable') : trans('common.enable'),
                            'url' => route('services.toggle', ['id' => Hashids::encode($service->id)])
                        ],
                        [
                            'type' => 'confirm',
                            'option' => trans('common.delete.item'),
                            'url' => route('services.destroy', [
                                'id' => Hashids::encode($service->id)
                            ]),
                            'method' => 'DELETE'
                        ],
                    ]
                ],
                [
                    'option' => trans('common.back'),
                    'url' => url()->previous()
                ],
            ]
        ])

        @include('app.services.info')

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
                    'data' => $service->vouchers->take(20),
                    'listHeading' => 'app.services.vouchers.list-heading',
                    'listRow' => 'app.services.vouchers.list-row'
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
        generate_chart('myChart', Array.from({!! $data->toJson() !!}))
    </script>
@endsection