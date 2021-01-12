@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('room', $room) }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('rooms.title'),
            'url' => route('rooms.index'),
            'options' => [
                [
                    'option' => trans('common.back'),
                    'url' => url()->previous()
                ],
            ]
        ])

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <h2 class="text-center">@lang('common.editionOf') @lang('rooms.title')</h2>

                <div class="row mb-4">
                    <div class="col-12">Hotel:</div>
                    <div class="col-12 mt-2">
                        <h4>{{ $room->hotel->business_name }}</h4>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-12">@lang('common.floor')):</div>
                    <div class="col-12 mt-2">
                        <h4>@lang('common.number') {{ $room->floor }}</h4>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-12">@lang('common.number'):</div>
                    <div class="col-12 mt-2">
                        <h4>{{ $room->number }}</h4>
                    </div>
                </div>

                <form action="{{ route('rooms.update', ['id' => id_encode($room->id)]) }}" method="POST">
                    @csrf()
                    @method('PUT')

                    <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                        <label for="description">@lang('common.description'):</label>
                        <textarea name="description" id="description" class="form-control" cols="30" rows="5" maxlength="500" required>{{ $room->description }}</textarea>

                        @if ($errors->has('description'))
                            <span class="help-block">
                                <strong>{{ $errors->first('description') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('is_suite') ? ' has-error' : '' }}">
                        <label for="pwd">@lang('rooms.type'):</label>
                        <select class="form-control selectpicker" title="Es una suite?" name="is_suite" id="is_suite" required>
                            <option value="0" {{ !$room->is_suite ? 'selected' : '' }}>@lang('common.no')</option>
                            <option value="1" {{ $room->is_suite ? 'selected' : '' }}>@lang('common.yes')</option>
                        </select>

                        @if ($errors->has('is_suite'))
                            <span class="help-block">
                                <strong>{{ $errors->first('is_suite') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
                        <label for="price">@lang('common.price'):</label>
                        <input type="number" class="form-control" name="price" id="price" value="{{ number_format($room->price, 0, '.', '') }}" min="1" max="999999" required>

                        @if ($errors->has('price'))
                            <span class="help-block">
                                <strong>{{ $errors->first('price') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('min_price') ? ' has-error' : '' }}">
                        <label for="min_price">@lang('common.min_price'):</label>
                        <input type="number" class="form-control" name="min_price" id="min_price" value="{{ number_format($room->min_price, 0, '.', '') }}" min="1" max="999999" required>

                        @if ($errors->has('min_price'))
                            <span class="help-block">
                                <strong>{{ $errors->first('min_price') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('capacity') ? ' has-error' : '' }}">
                        <label for="capacity">@lang('common.capacity'):</label>
                        <input type="number" class="form-control" name="capacity" id="capacity" value="{{ $room->capacity }}" min="1" max="12" required>

                        @if ($errors->has('capacity'))
                            <span class="help-block">
                                <strong>{{ $errors->first('capacity') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('tax_status') ? ' has-error' : '' }}">
                        <label for="pwd">@lang('common.tax.status'):</label>
                        <select class="form-control selectpicker" title="Opcional" name="tax_status" id="tax_status">
                            @if ($room->tax > 0)
                                <option value="1" selected>@lang('common.with.tax')</option>
                                <option value="0">@lang('common.without.tax')</option>
                            @else
                                <option value="1">@lang('common.with.tax')</option>
                                <option value="0" selected>@lang('common.without.tax')</option>
                            @endif
                        </select>

                        @if ($errors->has('tax_status'))
                            <span class="help-block">
                                <strong>{{ $errors->first('tax_status') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('tax') ? ' has-error' : '' }}" style="display:{{ $room->tax > 0 ? 'block;' : 'none;' }}" id="tax-input">
                        <label for="tax">@lang('common.tax.title'):</label>
                        <input type="number" class="form-control" name="tax" id="tax" value="{{ $room->tax > 0 ? number_format($room->tax, 2, '.', '') : '' }}" min="0.01" max="0.5" step="0.01">

                        @if ($errors->has('tax'))
                            <span class="help-block">
                                <strong>{{ $errors->first('tax') }}</strong>
                            </span>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary">
                        @lang('common.update')
                    </button>
                    <a href="{{ route('rooms.index') }}" class="btn btn-secondary">
                        @lang('common.back')
                    </a>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="spacer-md"></div>
            </div>
        </div>
    </div>

@endsection
