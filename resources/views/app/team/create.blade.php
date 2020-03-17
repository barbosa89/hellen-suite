@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('team') }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('users.team'),
            'url' => route('team.index'),
            'options' => [
                [
                    'option' => trans('common.back'),
                    'url' => url()->previous()
                ],
            ]
        ])

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <h2 class="text-center">@lang('team.new')</h2>
                <form action="{{ route('team.store') }}" method="POST">
                    @csrf()

                    <div class="form-group{{ $errors->has('hotel') ? ' has-error' : '' }}">
                        <label for="hotel">@lang('team.workplace'):</label>
                        <select class="form-control selectpicker" name="hotel" id="hotel" required>
                            @foreach ($hotels as $hotel)
                                <option value="{{ Hashids::encode($hotel->id) }}">{{ $hotel->business_name }}</option>
                            @endforeach
                        </select>

                        @if ($errors->has('hotel'))
                            <span class="help-block">
                                <strong>{{ $errors->first('hotel') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label for="name">@lang('common.name'):</label>
                        <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}" required maxlength="191" placeholder="{{ trans('common.name') }}">

                        @if ($errors->has('name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label for="email">@lang('common.email'):</label>
                        <input type="email" class="form-control" name="email" id="email" value="{{ old('email') }}" maxlength="100" placeholder="{{ trans('common.email') }}" required>

                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('role') ? ' has-error' : '' }}">
                        <label for="role">Rol:</label>
                        <select class="form-control selectpicker" name="role" id="role" required>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">{{ trans('users.' . $role->name) }}</option>
                            @endforeach
                        </select>

                        @if ($errors->has('role'))
                            <span class="help-block">
                                <strong>{{ $errors->first('role') }}</strong>
                            </span>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary">@lang('common.create')</button>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">@lang('common.back')</a>
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

@section('scripts')
    <script type="text/javascript">
        $("#type").on('change', function(e) {
            if (this.value == 'headquarters') {
                if ($('#main-hotel').is(':hidden')) {
                    $('#main-hotel').fadeIn();
                }
            } else {
                if ($('#main-hotel').is(':visible')) {
                    $('#main-hotel').fadeOut();
                }
            }
        });
    </script>
@endsection