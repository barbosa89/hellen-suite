@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('accounts') }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('accounts.title'),
            'url' => route('accounts.password.change'),
            'options' => [
                [
                    'option' => trans('common.back'),
                    'url' => url()->previous()
                ],
            ]
        ])

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <h2 class="text-center">@lang('common.password')</h2>
                <form action="{{ route('accounts.password.update') }}" method="POST">
                    @csrf()

                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                        <label for="password">@lang('common.password'):</label>
                        <input type="password" class="form-control" name="password" id="password" value="{{ old('password') }}" required minlength="4" maxlength="16" placeholder="{{ trans('common.required') }}">

                        @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('new_password') ? ' has-error' : '' }}">
                        <label for="new_password">@lang('accounts.password.new'):</label>
                        <input type="password" class="form-control" name="new_password" id="new_password" value="{{ old('new_password') }}" required minlength="8" maxlength="16" placeholder="{{ trans('common.required') }}">

                        @if ($errors->has('new_password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('new_password') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('new_password_confirmation') ? ' has-error' : '' }}">
                        <label for="new_password_confirmation">@lang('login.confirmPassword'):</label>
                        <input type="password" class="form-control" name="new_password_confirmation" id="new_password_confirmation" value="{{ old('new_password_confirmation') }}" required minlength="8" maxlength="16" placeholder="{{ trans('common.required') }}">

                        @if ($errors->has('new_password_confirmation'))
                            <span class="help-block">
                                <strong>{{ $errors->first('new_password_confirmation') }}</strong>
                            </span>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary">@lang('common.update')</button>
                    <a href="{{ url()->previous() }}" class="btn btn-default">
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
