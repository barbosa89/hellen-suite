<div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
    <label for="type">@lang('common.type'):</label>
    <select name="type" id="type" class="form-control selectpicker" required>
        <option value="{{ Hashids::encode($vehicle->type->id) }}" selected>{{ trans('vehicles.' . $vehicle->type->type) }}</option>
        @foreach ($types as $type)
            <option value="{{ Hashids::encode($type->id) }}">{{ trans('vehicles.' . $type->type) }}</option>
        @endforeach
    </select>

    @if ($errors->has('type'))
        <span class="help-block">
            <strong>{{ $errors->first('type') }}</strong>
        </span>
    @endif
</div>

<div class="form-group{{ $errors->has('registration') ? ' has-error' : '' }}">
    <label for="registration">@lang('vehicles.registration'):</label>
    <input type="text" class="form-control" name="registration" id="registration" value="{{ $vehicle->registration }}" placeholder="{{ trans('common.required') }}" required pattern="[A-Z0-9]+" title="{{ trans('common.onlyAlphanumeric') }}, {{ trans('common.capital') }}">

    @if ($errors->has('registration'))
        <span class="help-block">
            <strong>{{ $errors->first('registration') }}</strong>
        </span>
    @endif
</div>

<div class="form-group{{ $errors->has('brand') ? ' has-error' : '' }}">
    <label for="brand">@lang('common.brand'):</label>
    <input type="text" class="form-control" name="brand" id="brand" value="{{ $vehicle->brand }}" placeholder="{{ trans('common.required') }}" required pattern="[a-zA-Z]+" title="{{ trans('common.alphabetic') }}">

    @if ($errors->has('brand'))
        <span class="help-block">
            <strong>{{ $errors->first('brand') }}</strong>
        </span>
    @endif
</div>

<div class="form-group{{ $errors->has('color') ? ' has-error' : '' }}">
    <label for="color">Color:</label>
    <input type="text" class="form-control" name="color" id="color" value="{{ $vehicle->color }}" placeholder="{{ trans('common.required') }}" required required pattern="[a-zA-Z]+" title="{{ trans('common.alphabetic') }}">

    @if ($errors->has('color'))
        <span class="help-block">
            <strong>{{ $errors->first('color') }}</strong>
        </span>
    @endif
</div>