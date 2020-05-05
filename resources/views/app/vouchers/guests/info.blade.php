<div class="row">
    <div class="col-xs-4 col-sm-4 col-md-4 col-md-4">
        <h4>@lang('common.name'):</h4>
        <p>{{ $guest->name . ' ' . $guest->last_name }}</p>
    </div>
    <div class="col-xs-4 col-sm-4 col-md-4 col-md-4">
        <h4>@lang('common.idType'):</h4>
        <p>{{ trans('common.' . $guest->identificationType->type) }}</p>
    </div>
    <div class="col-xs-4 col-sm-4 col-md-4 col-md-4">
        <h4>@lang('common.idNumber'):</h4>
        <p>{{ $guest->dni }}</p>
    </div>
</div>

<input type="hidden" name="guest" value="{{ id_encode($guest->id) }}">

@include('partials.spacer', ['size' => 'xs'])