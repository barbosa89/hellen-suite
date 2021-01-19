@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('room', $room) }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('rooms.title'),
            'url' => route('rooms.index'),
            'search' => [
                'action' => route('rooms.search')
            ],
            'options' => [
                [
                    'option' => trans('common.options'),
                    'type' => 'dropdown',
                    'url' => [
                        [
                            'option' => trans('assets.add'),
                            'url' => '#'
                        ],
                        [
                            'option' => trans('products.add'),
                            'url' => '#'
                        ],
                        [
                            'type' => 'hideable',
                            'option' => trans('common.assign'),
                            'url' => route('vouchers.create', [
                                'hotel' => id_encode($room->hotel->id),
                                'rooms' => [
                                    id_encode($room->id)
                                ]
                            ]),
                            'show' => $room->isFree(),
                        ],
                        [
                            'type' => $room->canEnable() ? 'post' : 'hideable',
                            'option' => trans('common.enable'),
                            'url' => route('rooms.toggle'),
                            'inputs' => [
                                'room' => id_encode($room->id),
                                'status' => $room->available,
                            ],
                            'show' => $room->canEnable()
                        ],
                        [
                            'type' => $room->canDisable() ? 'post' : 'hideable',
                            'option' => trans('common.disable'),
                            'url' => route('rooms.toggle'),
                            'inputs' => [
                                'room' => id_encode($room->id),
                                'status' => $room->disabled,
                            ],
                            'show' => $room->canDisable()
                        ],
                        [
                            'type' => $room->canDoMaintenance() ? 'post' : 'hideable',
                            'option' =>  trans('rooms.maintenance'),
                            'url' => route('rooms.toggle'),
                            'inputs' => [
                                'room' => id_encode($room->id),
                                'status' => $room->maintenance,
                            ],
                            'show' => $room->canDoMaintenance()
                        ],
                        [
                            'option' => trans('common.edit'),
                            'url' => route('rooms.edit', [
                                'id' => id_encode($room->id)
                            ]),
                        ],
                        [
                            'type' => 'confirm',
                            'option' => trans('common.delete.item'),
                            'url' => route('rooms.destroy', [
                                'id' => id_encode($room->id)
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

        <div class="row">
            <div class="col-xs-3 col-sm-3 col-md-3 col-md-3">
                <h3>
                    @lang('rooms.room') No.:
                </h3>
                <p>
                    {{ $room->number }}

                    @if ($room->is_suite)
                        <i class="fas fa-star"></i>
                    @endif
                </p>
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-md-3">
                <h3>Hotel:</h3>
                <p>{{ $room->hotel->business_name }}</p>
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-md-3">
                <h3>@lang('common.status'):</h3>
                <p>
                    @include('app.rooms.status', ['status' => $room->status])
                </p>
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-md-3">
                <h3>@lang('common.floor'):</h3>
                <p>{{ $room->floor }}</p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-xs-3 col-sm-3 col-md-3 col-md-3">
                <h3>@lang('common.price'):</h3>
                <p>{{ number_format($room->price, 2, ',', '.') }}</p>
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-md-3">
                <h3>@lang('common.min_price'):</h3>
                <p>{{ number_format($room->min_price, 2, ',', '.') }}</p>
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-md-3">
                <h3>@lang('common.capacity')</h3>
                <p>{{ $room->capacity }}</p>
            </div>
            <div class="col-xs-3 col-sm-3 col-md-3 col-md-3">
                <h3>@lang('common.tax.title')</h3>
                <p>{{ number_format($room->tax * 100, 0, ',', '.') }}%</p>
            </div>
        </div>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="vouchers-tab" data-toggle="tab" href="#vouchers" role="tab" aria-controls="vouchers" aria-selected="true">
                    @lang('transactions.title')
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="charts-tab" data-toggle="tab" href="#charts" role="tab" aria-controls="charts" aria-selected="false">
                    @lang('common.chart')
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="assets-tab" data-toggle="tab" href="#assets" role="tab" aria-controls="assets" aria-selected="false">
                    @lang('assets.title')
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="products-tab" data-toggle="tab" href="#products" role="tab" aria-controls="products" aria-selected="false">
                    @lang('products.title')
                </a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="vouchers" role="tabpanel" aria-labelledby="vouchers-tab">
                @include('partials.list', [
                    'data' => $room->vouchers->take(20),
                    'listHeading' => 'app.rooms.vouchers.list-heading',
                    'listRow' => 'app.rooms.vouchers.list-row'
                ])
            </div>
            <div class="tab-pane fade" id="charts" role="tabpanel" aria-labelledby="charts-tab">
                <canvas id="myChart"></canvas>
            </div>
            <div class="tab-pane fade" id="assets" role="tabpanel" aria-labelledby="assets-tab">
                @include('partials.list', [
                    'data' => $room->assets,
                    'listHeading' => 'app.assets.list-heading',
                    'listRow' => 'app.assets.list-row',
                    'where' => null,
                ])
            </div>
            <div class="tab-pane fade" id="products" role="tabpanel" aria-labelledby="products-tab">
                @include('partials.list', [
                    'data' => $room->products,
                    'listHeading' => 'app.products.list-heading',
                    'listRow' => 'app.products.list-row',
                    'where' => null,
                ])
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
