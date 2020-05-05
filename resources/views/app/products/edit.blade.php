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
                    'option' => trans('common.back'),
                    'url' => route('products.show', ['id' => id_encode($product->id)])
                ],
            ]
        ])

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <h2 class="text-center">@lang('common.editionOf') @lang('products.title')</h2>
                <form action="{{ route('products.update', ['id' => id_encode($product->id)]) }}" method="POST">
                    @csrf()
                    @method('PUT')

                    <div class="form-group{{ $errors->has('hotel') ? ' has-error' : '' }}">
                        <label for="pwd">@lang('hotels.title'):</label>
                        <select class="form-control selectpicker" title="Elige un hotel o sede" name="hotel" id="hotel" readonly>
                                <option value="{{ id_encode($product->hotel->id) }}" selected>{{ $product->hotel->business_name }}</option>
                        </select>

                        @if ($errors->has('hotel'))
                            <span class="help-block">
                                <strong>{{ $errors->first('hotel') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                        <label for="description">@lang('common.description'):</label>
                        <input type="text" class="form-control" name="description" id="description" value="{{ $product->description }}" required maxlength="191" placeholder="{{ trans('common.required') }}">

                        @if ($errors->has('description'))
                            <span class="help-block">
                                <strong>{{ $errors->first('description') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('brand') ? ' has-error' : '' }}">
                        <label for="brand">@lang('common.brand'):</label>
                        <input type="text" class="form-control" name="brand" id="brand" value="{{ $product->brand }}" maxlength="50">

                        @if ($errors->has('brand'))
                            <span class="help-block">
                                <strong>{{ $errors->first('brand') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('reference') ? ' has-error' : '' }}">
                        <label for="reference">@lang('common.reference'):</label>
                        <input type="text" class="form-control" name="reference" id="reference" value="{{ $product->reference }}" maxlength="50">

                        @if ($errors->has('reference'))
                            <span class="help-block">
                                <strong>{{ $errors->first('reference') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
                        <label for="price">@lang('common.price'):</label>
                        <input type="number" class="form-control" name="price" id="price" value="{{ round($product->price, 0) }}" min="1" required placeholder="{{ trans('common.required') }}">

                        @if ($errors->has('price'))
                            <span class="help-block">
                                <strong>{{ $errors->first('price') }}</strong>
                            </span>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary">@lang('common.update')</button>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="spacer-md"></div>
            </div>
        </div>
    </div>

@endsection
