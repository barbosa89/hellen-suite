@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('prop', $prop) }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('props.title'),
            'url' => route('props.index'),
            'options' => [
                [
                    'option' => trans('common.back'),
                    'url' => route('props.show', [
                        'id' => id_encode($prop->id)
                    ])
                ],
            ]
        ])

        @include('app.props.info')

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <h2 class="text-center">@lang('common.report.of') @lang('props.title')</h2>
                <form action="{{ route('props.prop.report.export', ['id' => id_encode($prop->id)]) }}" method="POST">
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
                    <a href="{{ route('props.show', ['id' => id_encode($prop->id)]) }}" class="btn btn-default">
                        @lang('common.back')
                    </a>
                </form>
            </div>
        </div>
    </div>

@endsection