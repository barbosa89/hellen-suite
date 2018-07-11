@extends('layouts.app')

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('assets.title'),
            'url' => route('assets.index'),
            'options' => [
                [
                    'option' => trans('common.back'),
                    'url' => url()->previous()
                ],
            ]
        ])

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <h2 class="text-center">@lang('common.editionOf') @lang('assets.title')</h2>
                <form action="{{ route('assets.update', ['asset' => Hashids::encode($asset->id)]) }}" method="POST">
                    @csrf()
                    @method('PUT')
                    
                    <div class="form-group{{ $errors->has('number') ? ' has-error' : '' }}">
                        <label for="number">@lang('common.number'):</label>
                        <input type="text" class="form-control" name="number" id="number" value="{{ $asset->number }}" required maxlength="191" placeholder="{{ trans('common.required') }}">

                        @if ($errors->has('number'))
                            <span class="help-block">
                                <strong>{{ $errors->first('number') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                        <label for="description">@lang('common.description'):</label>
                        <input type="text" class="form-control" name="description" id="description" value="{{ $asset->description }}" required maxlength="191" placeholder="{{ trans('common.required') }}">

                        @if ($errors->has('description'))
                            <span class="help-block">
                                <strong>{{ $errors->first('description') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('brand') ? ' has-error' : '' }}">
                        <label for="brand">@lang('common.brand'):</label>
                        <input type="text" class="form-control" name="brand" id="brand" value="{{ $asset->brand }}" maxlength="50">

                        @if ($errors->has('brand'))
                            <span class="help-block">
                                <strong>{{ $errors->first('brand') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('model') ? ' has-error' : '' }}">
                        <label for="model">@lang('common.model'):</label>
                        <input type="text" class="form-control" name="model" id="model" value="{{ $asset->model }}" maxlength="50">

                        @if ($errors->has('model'))
                            <span class="help-block">
                                <strong>{{ $errors->first('model') }}</strong>
                            </span>
                        @endif
                    </div> 

                    <div class="form-group{{ $errors->has('reference') ? ' has-error' : '' }}">
                        <label for="reference">@lang('common.reference'):</label>
                        <input type="text" class="form-control" name="reference" id="reference" value="{{ $asset->reference }}" maxlength="50">

                        @if ($errors->has('reference'))
                            <span class="help-block">
                                <strong>{{ $errors->first('reference') }}</strong>
                            </span>
                        @endif
                    </div> 
                    
                    <div class="form-group{{ $errors->has('location') ? ' has-error' : '' }}">
                        <label for="location">@lang('common.location'):</label>
                        <input type="text" class="form-control" name="location" id="location" value="{{ $asset->location }}" maxlength="50">

                        @if ($errors->has('location'))
                            <span class="help-block">
                                <strong>{{ $errors->first('location') }}</strong>
                            </span>
                        @endif
                    </div> 

                    <div class="form-group{{ $errors->has('room') ? ' has-error' : '' }}">
                        <label for="pwd">@lang('common.assign'):</label>
                        <select class="form-control selectpicker" title="Opcional" name="room" id="room">
                            <option value="{{ Hashids::encode($asset->rooms->first()->id) }}" selected>
                                {{ $asset->rooms->first()->number . '-' . $asset->rooms->first()->description }}
                            </option>
                            
                            @foreach($rooms as $room)
                                <option value="{{ Hashids::encode($room->id) }}">{{ $room->number . '-' . $room->description }}</option>
                            @endforeach
                        </select>

                        @if ($errors->has('room'))
                            <span class="help-block">
                                <strong>{{ $errors->first('room') }}</strong>
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