@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('props') }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('props.title'),
            'url' => route('props.index'),
            'options' => [
                [
                    'option' => trans('common.back'),
                    'url' => url()->previous()
                ],
            ]
        ])

        <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                <h2>@lang('common.description'):</h2>
                <p>{{ $prop->description }}</p>
            </div>
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                <h2>@lang('common.quantity'):</h2>
                {{ $prop->quantity }}
            </div>
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                <h3>@lang('common.model'):</h3>
                <p>{{ $prop->hotel->business_name }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <h2 class="text-center">Reporte de @lang('props.title')</h2>
                <form action="{{ route('props.prop.report.export', ['id' => Hashids::encode($prop->id)]) }}" method="POST">
                    @csrf()

                    <div class="form-group{{ $errors->has('start') ? ' has-error' : '' }}">
                        <label for="start">@lang('common.startDate'):</label>
                        <input type="text" class="form-control datepicker" name="start" id="start" value="{{ old('start') }}" required>

                        @if ($errors->has('start'))
                            <span class="help-block">
                                <strong>{{ $errors->first('start') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('end') ? ' has-error' : '' }}">
                        <label for="end">@lang('common.endDate'):</label>
                        <input type="text" class="form-control datepicker" name="end" id="end" value="{{ old('end') }}" required>

                        @if ($errors->has('end'))
                            <span class="help-block">
                                <strong>{{ $errors->first('end') }}</strong>
                            </span>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary">@lang('common.query')</button>
                    <a href="{{ route('props.show', ['id' => Hashids::encode($prop->id)]) }}" class="btn btn-default">
                        @lang('common.back')
                    </a>
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