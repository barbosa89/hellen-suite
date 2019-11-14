@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('asset', $asset) }}
@endsection

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
                        <input type="text" class="form-control" name="number" id="number" value="{{ $asset->number }}" readonly maxlength="191" placeholder="{{ trans('common.required') }}">

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

                    <div class="form-group{{ $errors->has('serial_number') ? ' has-error' : '' }}">
                        <label for="serial_number">@lang('assets.serialNumber'):</label>
                        <input type="text" class="form-control" name="serial_number" id="serial_number" value="{{ $asset->serial_number }}" maxlength="50">

                        @if ($errors->has('serial_number'))
                            <span class="help-block">
                                <strong>{{ $errors->first('serial_number') }}</strong>
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

                    <div class="form-group{{ $errors->has('hotel') ? ' has-error' : '' }}">
                        <label for="pwd">@lang('hotels.title'):</label>
                        <select class="form-control selectpicker" title="Elige un hotel o sede" name="hotel" id="hotel" required onchange="listRoomsByHotel(this)">
                            <option value="{{ Hashids::encode($asset->hotel->id) }}" selected>{{ $asset->hotel->business_name }}</option>

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

                    <div class="form-group{{ $errors->has('room') ? ' has-error' : '' }}" id="room-list">
                        <label for="pwd">{{ trans('rooms.room') }} No.:</label>
                        <select class="form-control selectpicker" title="{{ trans('common.optional') }}" name="room" id="room">
                            @if (!empty($asset->room))
                                <option value="{{ Hashids::encode($asset->room->id) }}" selected>
                                    {{ $asset->room->number }}
                                </option>
                            @endif

                            @foreach($asset->hotel->rooms->where('id', '!=', $asset->room->id ?? null) as $room)
                                <option value="{{ Hashids::encode($room->id) }}">
                                    {{ $room->number }}
                                </option>
                            @endforeach
                        </select>

                        @if ($errors->has('room'))
                            <span class="help-block">
                                <strong>{{ $errors->first('room') }}</strong>
                            </span>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary">@lang('common.update')</button>
                    <button type="button" class="btn btn-default" id="remove-room">Quitar habitación</button>
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