<div class="form-group{{ $errors->has('query') ? ' has-error' : '' }}">
    <label for="query">@lang('common.search'):</label>
    <input type="string" class="form-control" name="query" id="query" value="{{ old('query') }}" onkeyup="search(this.value, event)" required>

    @if ($errors->has('query'))
        <span class="help-block">
            <strong>{{ $errors->first('query') }}</strong>
        </span>
    @endif
</div>