<div class="form-group{{ $errors->has('room') ? ' has-error' : '' }}">
    <label for="room">@lang('rooms.title'):</label>
    <select class="form-control selectpicker" title="{{ trans('rooms.chooseRoom') }}" name="room" id="room" required>
        @foreach($voucher->rooms as $room)
            <option value="{{ id_encode($room->id) }}">{{ $room->number }}</option>
        @endforeach
    </select>

    @if ($errors->has('room'))
        <span class="help-block">
            <strong>{{ $errors->first('room') }}</strong>
        </span>
    @endif
</div>

<div class="form-group{{ $errors->has('product') ? ' has-error' : '' }}">
    <label for="product">@lang('products.title'):</label>
    <select class="form-control selectpicker" title="{{ trans('products.chooseProduct') }}" name="product" id="product" required onchange="showTotal()">
        @foreach($products as $product)
            <option value="{{ id_encode($product->id) }}" data-max="{{ $product->quantity }}">
                {{ $product->description }} 
                {{ !empty($product->brand) ? $product->brand : '' }} 
                {{ !empty($product->reference) ? 'Ref. ' . $product->reference : '' }} 
            </option>
        @endforeach
    </select>

    @if ($errors->has('product'))
        <span class="help-block">
            <strong>{{ $errors->first('product') }}</strong>
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