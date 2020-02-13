<div class="form-group{{ $errors->has('guest') ? ' has-error' : '' }}">
    <label for="pwd">@lang('guests.guest'):</label>
    <select class="form-control selectpicker" title="{{ trans('common.chooseOption') }}" name="guest" id="guest" required>
        @foreach ($voucher->guests as $guest)
            <option value="{{ Hashids::encode($guest->id) }}">{{ $guest->full_name }}</option>
        @endforeach
    </select>

    @if ($errors->has('guest'))
        <span class="help-block">
            <strong>{{ $errors->first('guest') }}</strong>
        </span>
    @endif
</div>