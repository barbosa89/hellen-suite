<div class="form-group{{ $errors->has('room') ? ' has-error' : '' }}">
    <label for="room">@lang('rooms.room'):</label>
    <select class="form-control selectpicker" title="{{ trans('rooms.chooseRoom') }}" name="room" id="room" required>
        @foreach($invoice->rooms as $room)
            <option value="{{ Hashids::encode($room->id) }}">{{ $room->number }}</option>
        @endforeach
    </select>

    @if ($errors->has('room'))
        <span class="help-block">
            <strong>{{ $errors->first('room') }}</strong>
        </span>
    @endif
</div>