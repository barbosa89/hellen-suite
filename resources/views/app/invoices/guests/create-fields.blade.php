<div class="form-group{{ $errors->has('room') ? ' has-error' : '' }}">
    <label for="room">@lang('rooms.room'):</label>
    <select class="form-control selectpicker" title="{{ trans('rooms.chooseRoom') }}" name="room" id="room" required>
        @foreach($invoice->rooms as $room)
            @if ($room->status == '0' and $room->pivot->enabled == true)
                <option value="{{ Hashids::encode($room->id) }}" {{ $loop->first ? 'selected' : '' }}>{{ $room->number }}</option>
            @endif
        @endforeach
    </select>

    @if ($errors->has('room'))
        <span class="help-block">
            <strong>{{ $errors->first('room') }}</strong>
        </span>
    @endif
</div>

@if ($guests > 0)
    <div class="form-group{{ $errors->has('responsible_adult') ? ' has-error' : '' }}">
        <label for="responsible_adult">@lang('invoices.responsibleAdult'):</label>
        <select class="form-control selectpicker" title="{{ trans('common.onlyFor') . ' ' . strtolower(trans('invoices.minors'))  }}" name="responsible_adult" id="responsible_adult">
            @foreach($invoice->rooms as $room)
                @foreach($room->guests as $guest)
                    <option value="{{ Hashids::encode($guest->id) }}">{{ $guest->name . ' ' . $guest->last_name }}</option>
                @endforeach
            @endforeach
        </select>

        @if ($errors->has('responsible_adult'))
            <span class="help-block">
                <strong>{{ $errors->first('responsible_adult') }}</strong>
            </span>
        @endif
    </div>
@endif