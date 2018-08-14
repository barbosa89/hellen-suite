<div class="form-group{{ $errors->has('tin') ? ' has-error' : '' }}">
    <label for="tin">@lang('companies.tin'):</label>
    <input type="text" class="form-control" name="tin" id="tin" value="{{ old('tin') }}" placeholder="{{ trans('common.required') }}" required>

    @if ($errors->has('tin'))
        <span class="help-block">
            <strong>{{ $errors->first('tin') }}</strong>
        </span>
    @endif
</div>

<div class="form-group{{ $errors->has('business_name') ? ' has-error' : '' }}">
    <label for="business_name">@lang('companies.businessName'):</label>
    <input type="text" class="form-control" name="business_name" id="business_name" value="{{ old('business_name') }}" placeholder="{{ trans('common.required') }}" required>

    @if ($errors->has('business_name'))
        <span class="help-block">
            <strong>{{ $errors->first('business_name') }}</strong>
        </span>
    @endif
</div>

<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
    <label for="email">@lang('common.email'):</label>
    <input type="email" class="form-control" name="email" id="email" value="{{ old('email') }}" placeholder="{{ trans('common.optional') }}">

    @if ($errors->has('email'))
        <span class="help-block">
            <strong>{{ $errors->first('email') }}</strong>
        </span>
    @endif
</div>

<div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
    <label for="address">@lang('common.address'):</label>
    <input type="text" class="form-control" name="address" id="address" value="{{ old('address') }}" placeholder="{{ trans('common.optional') }}">

    @if ($errors->has('address'))
        <span class="help-block">
            <strong>{{ $errors->first('address') }}</strong>
        </span>
    @endif
</div>

<div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
    <label for="phone">@lang('common.phone'):</label>
    <input type="text" class="form-control" name="phone" id="phone" value="{{ old('phone') }}" placeholder="{{ trans('common.optional') }}">

    @if ($errors->has('phone'))
        <span class="help-block">
            <strong>{{ $errors->first('phone') }}</strong>
        </span>
    @endif
</div>

<div class="form-group{{ $errors->has('mobile') ? ' has-error' : '' }}">
    <label for="mobile">@lang('common.mobile'):</label>
    <input type="text" class="form-control" name="mobile" id="mobile" value="{{ old('mobile') }}" placeholder="{{ trans('common.optional') }}">

    @if ($errors->has('mobile'))
        <span class="help-block">
            <strong>{{ $errors->first('mobile') }}</strong>
        </span>
    @endif
</div>
