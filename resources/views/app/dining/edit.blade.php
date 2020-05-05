@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('dining-service', $service) }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('dining.title'),
            'url' => route('dining.index'),
            'options' => [
                [
                    'option' => trans('common.back'),
                    'url' => url()->previous()
                ],
            ]
        ])

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <h2 class="text-center">@lang('common.editionOf') @lang('dining.menu.item')</h2>
                <form action="{{ route('dining.update', ['id' => id_encode($service->id)]) }}" method="POST">
                    @csrf()
                    @method('PUT')

                    <div class="form-group{{ $errors->has('hotel') ? ' has-error' : '' }}">
                        <label for="pwd">@lang('hotels.title'):</label>
                        <select class="form-control selectpicker" title="Elige un hotel o sede" name="hotel" id="hotel" readonly>
                                <option value="{{ id_encode($service->hotel->id) }}" selected>{{ $service->hotel->business_name }}</option>
                        </select>

                        @if ($errors->has('hotel'))
                            <span class="help-block">
                                <strong>{{ $errors->first('hotel') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                        <label for="description">@lang('common.description'):</label>
                        <input type="text" class="form-control" name="description" id="description" value="{{ $service->description }}" required maxlength="191" placeholder="{{ trans('common.required') }}">

                        @if ($errors->has('description'))
                            <span class="help-block">
                                <strong>{{ $errors->first('description') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
                        <label for="price">@lang('common.price'):</label>
                        <input type="number" class="form-control" name="price" id="price" value="{{ round($service->price, 0) }}" min="1" required placeholder="{{ trans('common.required') }}">

                        @if ($errors->has('price'))
                            <span class="help-block">
                                <strong>{{ $errors->first('price') }}</strong>
                            </span>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary">@lang('common.update')</button>
                    <a href="{{ url()->previous() }}" class="btn btn-link">@lang('common.back')</a>
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
