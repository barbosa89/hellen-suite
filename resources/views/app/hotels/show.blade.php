@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('hotel', $hotel) }}
@endsection

@section('content')
    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('hotels.title'),
            'url' => route('hotels.index'),
            'options' => [
                [
                    'option' => trans('common.edit'),
                    'url' => route('hotels.edit', [
                        'id' => id_encode($hotel->id)
                    ]),
                ],
                [
                    'option' => $hotel->status ? trans('common.disable') : trans('common.enable'),
                    'url' => route('hotels.toggle', [
                        'id' => id_encode($hotel->id)
                    ]),
                ],
                [
                    'type' => 'confirm',
                    'option' => trans('common.delete.item'),
                    'url' => route('hotels.destroy', [
                        'id' => id_encode($hotel->id)
                    ]),
                    'method' => 'DELETE'
                ],
            ]
        ])

        <div class="row mb-4">
            <div class="col-xs-2 col-sm-2 col-md-2 col-md-2">
                <img class="img-fluid" src="{{ empty($hotel->image) ? asset('/images/hotel.png') : asset(Storage::url($hotel->image)) }}" alt="{{ $hotel->business_name }}">
            </div>
            <div class="col-xs-10 col-sm-10 col-md-10 col-md-10">
                <div class="row">
                    <div class="col-xs-4 col-sm-4 col-md-4 col-md-4">
                        <h3>@lang('companies.businessName'):</h3>
                        <p>{{ $hotel->business_name }}</p>
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-md-4">
                        <h3>@lang('common.number'):</h3>
                        <p>{{ $hotel->tin }}</p>
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-md-4">
                        <h3>@lang('hotels.headquarters'):</h3>
                        <p>{{ $hotel->main ? $hotel->main->business_name : trans('common.doesnt.apply') }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-4 col-sm-4 col-md-4 col-md-4">
                        <h3>@lang('common.address'):</h3>
                        <p>{{ $hotel->address }}</p>
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-md-4">
                        <h3>@lang('common.phone'):</h3>
                        <p>{{ $hotel->phone }} {{ $hotel->mobile }}</p>
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-md-4">
                        <h3>@lang('common.email'):</h3>
                        <p>{{ $hotel->email }}</p>
                    </div>
                </div>
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
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="vouchers" role="tabpanel" aria-labelledby="vouchers-tab">
                @include('partials.list', [
                    'data' => $hotel->vouchers->take(20),
                    'listHeading' => 'app.hotels.vouchers.list-heading',
                    'listRow' => 'app.hotels.vouchers.list-row'
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
