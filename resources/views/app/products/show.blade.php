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
                            'option' => trans('products.increase'),
                            'url' => route('products.increase.form', [
                                'id' => Hashids::encode($product->id)
                            ]),
                        ],
                        [
                            'type' => 'divider'
                        ],
                        [
                            'option' => trans('common.edit'),
                            'url' => route('products.edit', [
                                'room' => Hashids::encode($product->id)
                            ]),
                        ],
                        [
                            'type' => 'confirm',
                            'option' => trans('common.delete'),
                            'url' => route('products.destroy', [
                                'id' => Hashids::encode($product->id)
                            ]),
                            'method' => 'DELETE'
                        ],
                    ]
                ],
                [
                    'option' => trans('common.back'),
                    'url' => route('products.index')
                ],
            ]
        ])

        <div class="row">
            <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                <h3>Hotel:</h3>
                <p>
                    <a href="{{ route('hotels.show', ['id' => Hashids::encode($product->hotel->id)]) }}">
                        {{ $product->hotel->business_name }}
                    </a>
                </p>
            </div>
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                <h2>@lang('common.description'):</h2>
                <p>{{ $product->description }}</p>
            </div>
            <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                <h2>@lang('common.brand'):</h2>
                {{ $product->brand ?? 'No definida' }}
            </div>
        </div>

        <div class="row">
            <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                <h3>@lang('common.reference'):</h3>
                <p>{{ $product->reference ?? 'No definida' }}</p>
            </div>
            <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                <h3>@lang('common.quantity'):</h3>
                <p>{{ $product->quantity }}</p>
            </div>
            <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                <h3>@lang('common.price'):</h3>
                <p>$ {{ round($product->price, 0) }}</p>
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