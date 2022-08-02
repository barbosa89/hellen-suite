@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('asset', $maintenance->maintainable) }}
@endsection

@section('content')
    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('assets.title'),
            'url' => route('assets.index'),
            'options' => [
                [
                    'option' => trans('common.back'),
                    'url' => route('assets.show', [
                        'id' => $maintenance->maintainable->hash
                    ]),
                ],
            ]
        ])

        @include('app.assets.info', ['asset' => $maintenance->maintainable])

        <div class="row my-4">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <h2 class="text-center">@lang('common.new') @lang('maintenances.maintenance')</h2>
                <form
                    action="{{ route('assets.maintenances.update', [
                            'asset' => $maintenance->maintainable->hash,
                            'maintenance' => $maintenance->hash,
                        ]) }}"
                        method="POST" enctype="multipart/form-data">
                    @csrf()
                    @method('PATCH')

                    <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
                        <label for="date">@lang('common.date'):</label>
                        <input
                            type="text"
                            class="form-control datepicker"
                            name="date" id="date"
                            value="{{ $maintenance->date }}"
                            required
                            placeholder="{{ trans('common.required') }}">

                        @if ($errors->has('date'))
                            <span class="help-block">
                                <strong>{{ $errors->first('date') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('commentary') ? ' has-error' : '' }}">
                        <label for="commentary">@lang('common.commentary'):</label>
                        <textarea
                            class="form-control"
                            name="commentary"
                            id="commentary"
                            cols="30"
                            rows="4"
                            required
                            placeholder="{{ trans('common.required') }}">{{ $maintenance->commentary }}</textarea>

                        @if ($errors->has('commentary'))
                            <span class="help-block">
                                <strong>{{ $errors->first('commentary') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('value') ? ' has-error' : '' }}">
                        <label for="value">@lang('common.value'):</label>
                        <input type="number" class="form-control" name="value" id="value" value="{{ $maintenance->value ?? '' }}" min="0.1" max="99999999" step="0.01">

                        @if ($errors->has('value'))
                            <span class="help-block">
                                <strong>{{ $errors->first('value') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('invoice') ? ' has-error' : '' }}">
                        <label for="invoice">@lang('common.invoice'): <small>Un nuevo documento reemplaza el actual, en caso contrario, lo agrega</small></label>
                        <input type="file" class="form-control" name="invoice" id="invoice" accept="image/png, image/jpeg">

                        @if ($errors->has('invoice'))
                            <span class="help-block">
                                <strong>{{ $errors->first('invoice') }}</strong>
                            </span>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary">@lang('common.register')</button>
                    <a href="{{ route('assets.show', ['id' => $maintenance->maintainable->hash]) }}" class="btn btn-default">
                        @lang('common.back')
                    </a>
                </form>
            </div>
        </div>
    </div>
@endsection
