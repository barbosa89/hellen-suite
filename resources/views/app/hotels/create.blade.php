@extends('layouts.panel')

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => 'Hoteles',
            'url' => route('hotels.index'),
            'options' => [
                [
                    'option' => trans('common.back'),
                    'url' => url()->previous()
                ],
            ]
        ])

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <h2 class="text-center">@lang('common.creationOf') Hotel</h2>
                <form action="{{ route('hotels.store') }}" method="POST">
                    @csrf()

                    <div class="form-group{{ $errors->has('business_name') ? ' has-error' : '' }}">
                        <label for="business_name">Razón social:</label>
                        <input type="text" class="form-control" name="business_name" id="business_name" value="{{ old('business_name') }}" required maxlength="191" placeholder="{{ trans('common.required') }}">

                        @if ($errors->has('business_name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('business_name') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('tin') ? ' has-error' : '' }}">
                        <label for="tin">NIT:</label>
                        <input type="text" class="form-control" name="tin" id="tin" value="{{ old('tin') }}" maxlength="30" placeholder="{{ trans('common.required') }}" required>

                        @if ($errors->has('tin'))
                            <span class="help-block">
                                <strong>{{ $errors->first('tin') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
                        <label for="address">@lang('common.address'):</label>
                        <input type="text" class="form-control" name="address" id="address" value="{{ old('address') }}" maxlength="100">

                        @if ($errors->has('address'))
                            <span class="help-block">
                                <strong>{{ $errors->first('address') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                        <label for="phone">@lang('common.phone'):</label>
                        <input type="string" class="form-control" name="phone" id="phone" value="{{ old('phone') }}" maxlength="10">

                        @if ($errors->has('phone'))
                            <span class="help-block">
                                <strong>{{ $errors->first('phone') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('mobile') ? ' has-error' : '' }}">
                        <label for="mobile">@lang('common.mobile'):</label>
                        <input type="string" class="form-control" name="mobile" id="mobile" value="{{ old('mobile') }}" maxlength="10">

                        @if ($errors->has('mobile'))
                            <span class="help-block">
                                <strong>{{ $errors->first('mobile') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label for="email">@lang('common.email'):</label>
                        <input type="email" class="form-control" name="email" id="email" value="{{ old('email') }}" maxlength="100">

                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary">@lang('common.create')</button>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Volver</a>
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