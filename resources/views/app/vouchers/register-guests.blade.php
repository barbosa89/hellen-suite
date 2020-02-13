@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('invoice', $voucher) }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('vouchers.title'),
            'url' => route('invoices.index'),
            'options' => [
                [
                    'option' => trans('common.options'),
                    'url' => '#'
                ],
                [
                    'option' => trans('common.back'),
                    'url' => url()->previous()
                ],
            ]
        ])

        <div class="row">
            <div class="col-xs-4 col-sm-4 col-md-4 col-md-4">
                <h2>@lang('invoices.invoice'):</h2>
                <p>{{ $voucher->number }}</p>
            </div>
            <div class="col-xs-4 col-sm-4 col-md-4 col-md-4">
                <h2>@lang('common.value'):</h2>
                <p>{{ number_format($voucher->value, 2, ',', '.') }}</p>
            </div>
            <div class="col-xs-4 col-sm-4 col-md-4 col-md-4">
                <h2>@lang('common.reservation'):</h2>
                <p>{{ $voucher->reservation ? trans('common.yes') : trans('common.no') }}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4 col-sm-4 col-md-4 col-md-4">
                <h2>@lang('invoices.forCompany'):</h2>
                <p>{{ $voucher->for_company ? trans('common.yes') : trans('common.no') }}</p>
            </div>
            <div class="col-xs-4 col-sm-4 col-md-4 col-md-4">
                <h2>@lang('invoices.tourism'):</h2>
                <p>{{ $voucher->are_tourists ? trans('common.yes') : trans('common.no') }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="spacer-md"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h3 class="text-center">@lang('rooms.addRoom')</h3>

                <form action="{{ route('invoices.rooms.store', ['id' => Hashids::encode($voucher->id)]) }}" method="POST">
                    @csrf()

                    <div class="form-group{{ $errors->has('room') ? ' has-error' : '' }}">
                        <label for="room">@lang('rooms.title'):</label>
                        <select class="form-control selectpicker" title="{{ trans('rooms.chooseRoom') }}" name="room" id="room" required>
                            @foreach($rooms as $room)
                                <option value="{{ Hashids::encode($room->id) }}">{{ $room->number }}</option>
                            @endforeach
                        </select>

                        @if ($errors->has('room'))
                            <span class="help-block">
                                <strong>{{ $errors->first('room') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="rooms-description">
                        @foreach($rooms as $room)
                            <div class="alert alert-info" id="description-{{ Hashids::encode($room->id) }}" style="display:none">
                                <p>{{ $room->description }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="form-group{{ $errors->has('start') ? ' has-error' : '' }}">
                        <label for="start">@lang('common.startDate'):</label>
                        <input type="string" class="form-control datepicker" name="start" id="start" value="{{ old('start') }}" min="1" max="999999" required>

                        @if ($errors->has('start'))
                            <span class="help-block">
                                <strong>{{ $errors->first('start') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('end') ? ' has-error' : '' }}">
                        <label for="end">@lang('common.endDate'):</label>
                        <input type="string" class="form-control datepicker" name="end" id="end" value="{{ old('end') }}" min="1" max="999999" required>

                        @if ($errors->has('end'))
                            <span class="help-block">
                                <strong>{{ $errors->first('end') }}</strong>
                            </span>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary">@lang('common.add')</button>
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