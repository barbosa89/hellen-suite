@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('increase', $product) }}
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
                            'option' => trans('common.seeMore'),
                            'url' => route('products.show', [
                                'id' => Hashids::encode($product->id)
                            ])
                        ],
                        [
                            'option' => trans('common.edit'),
                            'url' => route('products.edit', [
                                'id' => Hashids::encode($product->id)
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
                <h2>Hotel:</h2>
                <p>
                    <a href="{{ route('hotels.show', ['id' => Hashids::encode($product->hotel->id)]) }}">
                        {{ $product->hotel->business_name }}
                    </a>
                </p>
            </div>
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                <h2>@lang('common.description'):</h2>
                <p>
                    <a href="{{ route('products.show', ['id' => Hashids::encode($product->id)]) }}">
                        {{ $product->description }}
                    </a>
                </p>
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
                <h3>@lang('products.increase')</h3>

                <form action="{{ route('products.increase', ['id' => Hashids::encode($product->id)]) }}" method="POST">
                    @csrf()

                    <div class="form-group{{ $errors->has('quantity') ? ' has-error' : '' }}">
                        <input type="number" class="form-control" name="quantity" id="quantity" value="{{ old('quantity') }}" min="1" required placeholder="{{ trans('common.required') }}">

                        @if ($errors->has('quantity'))
                            <span class="help-block">
                                <strong>{{ $errors->first('quantity') }}</strong>
                            </span>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary">@lang('products.increase')</button>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="spacer-md"></div>
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection
