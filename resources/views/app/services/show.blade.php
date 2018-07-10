@extends('layouts.app')

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('products.title'),
            'url' => route('products.index'),
            'options' => [
                [
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
                    'url' => url()->previous()
                ],
            ]
        ])

        <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                <h2>@lang('common.description'):</h2>
                <p>{{ $product->description }}</p>
            </div>
            <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                <h2>@lang('common.brand'):</h2>
                {{ $product->brand }}
            </div>
            <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                <h3>@lang('common.reference'):</h3>
                <p>{{ $product->reference }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                <h3>@lang('common.reference'):</h3>
                <p>{{ $product->reference }}</p>
            </div>
            <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                <h3>@lang('common.quantity'):</h3>
                <p>{{ $product->quantity }}</p>
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