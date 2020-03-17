@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('member', $member) }}
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
                <h2 class="text-center">@lang('team.assignment')</h2>

                <div class="row mb-4">
                    <div class="col-12">@lang('team.member'):</div>
                    <div class="col-12 mt-2">
                        <h4>{{ $member->name }}</h4>
                    </div>
                </div>

                <form action="{{ route('team.assign.attach', ['id' => Hashids::encode($member->id)]) }}" method="POST">
                    @csrf()

                    <div class="form-group{{ $errors->has('hotel') ? ' has-error' : '' }}">
                        <label for="hotel">Sede laboral:</label>
                        <select class="form-control selectpicker" name="hotel" id="hotel" required>
                            @if ($member->headquarters->count() > 0)
                                @foreach ($member->headquarters as $hotel)
                                    <option value="{{ Hashids::encode($hotel->id) }}" selected>{{ $hotel->business_name }}</option>
                                @endforeach
                            @endif

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

                    <button type="submit" class="btn btn-primary">@lang('common.assign')</button>
                    <a href="{{ route('team.index') }}" class="btn btn-secondary">@lang('common.back')</a>
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