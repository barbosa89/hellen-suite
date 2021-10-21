<div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
    <label for="type">@lang('common.idType'):</label>
    <select class="form-control selectpicker" title="{{ trans('users.choose.identification') }}" name="type" id="type" required>
        <option value="{{ $guest->identificationType->hash }}" selected>
            {{ $guest->identificationType->description }}
        </option>

        @foreach($identificationTypes as $type)
            <option value="{{ $type->hash }}">{{ $type->description }}</option>
        @endforeach
    </select>

    @if ($errors->has('type'))
        <span class="help-block">
            <strong>{{ $errors->first('type') }}</strong>
        </span>
    @endif
</div>

<div class="form-group{{ $errors->has('dni') ? ' has-error' : '' }}">
    <label for="dni">@lang('common.number'):</label>
    <input type="text" class="form-control" name="dni" id="dni" value="{{ $guest->dni }}" required>

    @if ($errors->has('dni'))
        <span class="help-block">
            <strong>{{ $errors->first('dni') }}</strong>
        </span>
    @endif
</div>

<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
    <label for="name">@lang('common.name')(s):</label>
    <input type="text" class="form-control" name="name" id="name" value="{{ $guest->name }}" required>

    @if ($errors->has('name'))
        <span class="help-block">
            <strong>{{ $errors->first('name') }}</strong>
        </span>
    @endif
</div>

<div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
    <label for="last_name">@lang('common.lastname')(s):</label>
    <input type="text" class="form-control" name="last_name" id="last_name" value="{{ $guest->last_name }}" required>

    @if ($errors->has('last_name'))
        <span class="help-block">
            <strong>{{ $errors->first('last_name') }}</strong>
        </span>
    @endif
</div>

<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
    <label for="email">@lang('common.email'):</label>
    <input type="email" class="form-control" name="email" id="email" value="{{ $guest->email }}">

    @if ($errors->has('email'))
        <span class="help-block">
            <strong>{{ $errors->first('email') }}</strong>
        </span>
    @endif
</div>

<div class="form-group{{ $errors->has('address') ? ' has-error' : '' }}">
    <label for="address">@lang('common.address'):</label>
    <input type="text" class="form-control" name="address" id="address" value="{{ $guest->address }}">

    @if ($errors->has('address'))
        <span class="help-block">
            <strong>{{ $errors->first('address') }}</strong>
        </span>
    @endif
</div>

<div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
    <label for="phone">@lang('common.phone'):</label>
    <input type="tel" class="form-control" name="phone" id="phone" value="{{ $guest->phone }}">

    @if ($errors->has('phone'))
        <span class="help-block">
            <strong>{{ $errors->first('phone') }}</strong>
        </span>
    @endif
</div>

<div class="form-group{{ $errors->has('profession') ? ' has-error' : '' }}">
    <label for="profession">@lang('guests.profession'):</label>
    <input type="text" class="form-control" name="profession" id="profession" value="{{ $guest->profession }}">

    @if ($errors->has('profession'))
        <span class="help-block">
            <strong>{{ $errors->first('profession') }}</strong>
        </span>
    @endif
</div>

<div class="form-group{{ $errors->has('gender') ? ' has-error' : '' }}">
    <label for="gender">@lang('common.gender'):</label>
    <select class="form-control selectpicker" title="{{ trans('guests.choose.gender') }}" name="gender" id="gender" required>
        @if ($guest->gender)
            <option value="{{ $guest->gender }}" selected>{{ trans('common.' . $guest->gender) }}</option>
        @endif

        @foreach ($genders as $gender => $description)
            <option value="{{ $gender }}">{{ $description }}</option>
        @endforeach
    </select>

    @if ($errors->has('gender'))
        <span class="help-block">
            <strong>{{ $errors->first('gender') }}</strong>
        </span>
    @endif
</div>

<div class="form-group{{ $errors->has('nationality') ? ' has-error' : '' }}">
    <label for="nationality">@lang('guests.birth.country'):</label>
    <select class="form-control selectpicker" title="{{ trans('common.chooseOption') }}" name="nationality" id="nationality" required>
            <option value="{{ id_encode($guest->country->id) }}" selected>{{ $guest->country->name }}</option>
        @foreach ($countries as $country)
            <option value="{{ id_encode($country->id) }}">{{ $country->name }}</option>
        @endforeach
    </select>

    @if ($errors->has('nationality'))
        <span class="help-block">
            <strong>{{ $errors->first('nationality') }}</strong>
        </span>
    @endif
</div>

<div class="form-group{{ $errors->has('birthdate') ? ' has-error' : '' }}">
    <label for="birthdate">@lang('common.birthdate'):</label>
    <input type="string" class="form-control datepicker" name="birthdate" id="birthdate" value="{{ $guest->birthdate }}">

    @if ($errors->has('birthdate'))
        <span class="help-block">
            <strong>{{ $errors->first('birthdate') }}</strong>
        </span>
    @endif
</div>
