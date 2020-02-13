<div class="form-group{{ $errors->has('service') ? ' has-error' : '' }}">
    <label for="service">@lang('services.title'):</label>
    <select class="form-control selectpicker" title="{{ trans('services.chooseService') }}" name="service" id="service" required onchange="showTotal()">
        @foreach($services as $service)
            <option value="{{ Hashids::encode($service->id) }}">
                {{ $service->description }} 
            </option>
        @endforeach
    </select>

    @if ($errors->has('service'))
        <span class="help-block">
            <strong>{{ $errors->first('service') }}</strong>
        </span>
    @endif
</div>

<div class="form-group{{ $errors->has('quantity') ? ' has-error' : '' }}">
    <label for="quantity">@lang('common.quantity'):</label>
    <input type="string" pattern="[0-9]" class="form-control" name="quantity" id="quantity" min="1" value="{{ old('quantity') }}" required onkeyup="showTotal()">

    @if ($errors->has('quantity'))
        <span class="help-block">
            <strong>{{ $errors->first('quantity') }}</strong>
        </span>
    @endif
</div>