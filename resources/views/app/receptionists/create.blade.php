@extends('layouts.app')

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('users.title'),
            'url' => route('users.index'),
            'options' => [
                [
                    'option' => trans('common.back'),
                    'url' => route('receptionists.index')
                ],
            ]
        ])

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <h2 class="text-center">@lang('common.creationOf') @lang('users.title')</h2>
                <form action="{{ route('receptionists.store') }}" method="POST">
                    @csrf()
                    
                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                        <label for="name">@lang('common.name'):</label>
                        <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}" required>

                        @if ($errors->has('name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
                    </div>
                    
                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                        <label for="email">@lang('common.email'):</label>
                        <input type="email" class="form-control" name="email" id="email" value="{{ old('email') }}" required>

                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                    <div class="form-group{{ $errors->has('role') ? ' has-error' : '' }}">
                        <label for="pwd">Rol:</label>
                        <select class="form-control selectpicker" title="{{ trans('users.chooseRole') }}" name="role" id="role" required>
                            @foreach($roles as $role)
                                @if($loop->first)
                                    <option selected value="{{ Hashids::encode($role->id) }}">{{ trans('users.'. $role->name) }}</option>
                                @else
                                    <option value="{{ Hashids::encode($role->id) }}">{{ trans('users.'. $role->name) }}</option>
                                @endif
                            @endforeach
                        </select>

                        @if ($errors->has('role'))
                            <span class="help-block">
                                <strong>{{ $errors->first('role') }}</strong>
                            </span>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-primary">@lang('common.create')</button>
                </form> 
            </div>
        </div>
    </div>

@endsection