<div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
    <label for="description">@lang('common.description'):</label>
    <textarea class="form-control" name="description" id="description" cols="30" rows="3" required>
        {{ old('description') }}
    </textarea>

    @if ($errors->has('description'))
        <span class="help-block">
            <strong>{{ $errors->first('description') }}</strong>
        </span>
    @endif
</div>

<div class="form-group{{ $errors->has('value') ? ' has-error' : '' }}">
    <label for="value">@lang('common.value'):</label>
    <input type="number" class="form-control" name="value" id="value" required value="{{ old('value') }}" min="0.01" max="999999" step="0.01">

    @if ($errors->has('value'))
        <span class="help-block">
            <strong>{{ $errors->first('value') }}</strong>
        </span>
    @endif
</div>