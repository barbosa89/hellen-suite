
<h4>@lang('vouchers.registry')</h4>

<div class="form-group{{ $errors->has('registry') ? ' has-error' : '' }}">
    <select class="form-control selectpicker" title="Tipo de registro" name="registry" id="registry" required>
        <option value="checkin">Registro de ingreso</option>
        <option value="reservation">Reservación</option>
    </select>

    @if ($errors->has('registry'))
        <span class="help-block">
            <strong>{{ $errors->first('registry') }}</strong>
        </span>
    @endif
</div>
