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

        <div class="row">
            <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                <h3>Hotel:</h3>
                <p>
                    <a href="{{ route('hotels.show', ['id' => Hashids::encode($service->hotel->id)]) }}">
                        {{ $service->hotel->business_name }}
                    </a>
                </p>
            </div>
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                <h2>@lang('common.description'):</h2>
                <p>{{ $service->description }} <i class="fas fa-{{ $service->status ? 'check' : 'times-circle' }}"></i></p>
            </div>
            <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                <h2>@lang('common.brand'):</h2>
                {{ round($service->price, 0) }}
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="spacer-xs"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h3>@lang('common.chart')</h3>

                <div class="well">
                    <h4>Gráfica aquí</h4>
                </div>
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection
