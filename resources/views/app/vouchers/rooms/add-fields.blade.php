<div class="form-group{{ $errors->has('room') ? ' has-error' : '' }}">
    <label for="room">@lang('rooms.title'):</label>
    <select class="form-control selectpicker" title="{{ trans('rooms.chooseRoom') }}" name="room" id="room" required>
        @foreach($rooms as $room)
            <option value="{{ Hashids::encode($room->id) }}">{{ $room->number }}</option>
        @endforeach
    </select>

    @if ($errors->has('room'))
        <span class="help-block">
            <strong>{{ $errors->first('room') }}</strong>
        </span>
    @endif
</div>

<div class="form-group{{ $errors->has('start') ? ' has-error' : '' }}">
    <label for="start">@lang('common.startDate'):</label>
    <input type="string" class="form-control datepicker" name="start" id="start" value="{{ old('start') }}" required>

    @if ($errors->has('start'))
        <span class="help-block">
            <strong>{{ $errors->first('start') }}</strong>
        </span>
    @endif
</div>

<div class="form-group{{ $errors->has('end') ? ' has-error' : '' }}">
    <label for="end">@lang('common.endDate'):</label>
    <input type="string" class="form-control datepicker" name="end" id="end" value="{{ old('end') }}">

    @if ($errors->has('end'))
        <span class="help-block">
            <strong>{{ $errors->first('end') }}</strong>
        </span>
    @endif
</div>