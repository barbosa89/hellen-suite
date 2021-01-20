@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('assets') }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('assets.title'),
            'url' => route('assets.index'),
            'options' => [
                [
                    'option' => trans('rooms.number', ['number' => $room->number]),
                    'url' => route('rooms.show', [
                        'id' => id_encode($room->id),
                    ])
                ],
            ]
        ])

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <h2 class="text-center">@lang('assets.title') @lang('common.assign')</h2>

                <form action="{{ route('assets.assign', ['room' => id_encode($room->id)]) }}" method="POST">
                    @csrf()

                    <div class="form-group{{ $errors->has('room') ? ' has-error' : '' }}">
                        <label for="pwd">Hotel:</label>
                        <input type="text" class="form-control" name="room" id="room" value="{{ $room->hotel->business_name }}" readonly>
                    </div>

                    <div class="form-group{{ $errors->has('room') ? ' has-error' : '' }}">
                        <label for="pwd">@lang('rooms.room') No.:</label>
                        <input type="text" class="form-control" name="room" id="room" value="{{ $room->number }}" readonly>
                    </div>

                    <div class="form-group{{ $errors->has('asset') ? ' has-error' : '' }}">
                        <label for="pwd">@lang('assets.title'):</label>
                        <select class="form-control selectpicker" title="{{ trans('common.chooseOption') }}" name="asset" id="asset" required>
                            @foreach ($assets as $asset)
                                <option value="{{ id_encode($asset->id) }}" {{ $loop->first ? 'selected' : '' }}>
                                    {{ $asset->description }} No. {{ $asset->number }}
                                </option>
                            @endforeach
                        </select>

                        @if ($errors->has('asset'))
                            <span class="help-block">
                                <strong>{{ $errors->first('asset') }}</strong>
                            </span>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary">
                        @lang('common.assign')
                    </button>
                    <a href="{{ route('assets.index') }}" class="btn btn-default">
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
