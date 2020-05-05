@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('voucher', $voucher) }}
@endsection

@section('content')

    @include('partials.page-header', [
        'title' => trans('vouchers.title'),
        'url' => route('vouchers.index'),
        'options' => [
            [
                'option' => trans('vouchers.registerGuests'),
                'url' => route('vouchers.guests.search', ['id' => id_encode($voucher->id)])
            ],
            [
                'option' => $voucher->company ? trans('vouchers.linkNewCompany') : trans('vouchers.linkCompany'),
                'url' => route('vouchers.companies.search', [
                    'id' => id_encode($voucher->id)
                ]),
            ],
            [
                'option' => 'Volver al recibo',
                'url' => route('vouchers.show', [
                    'id' => id_encode($voucher->id)
                ])
            ]
        ]
    ])

    @include('app.vouchers.info')

    <h3 class="text-center">Agregar habitación</h3>
    <form class="mt-4" action="{{ route('vouchers.rooms.add', ['id' => id_encode($voucher->id)]) }}" method="POST" accept-charset="utf-8">
        @csrf
        <div class="form-group{{ $errors->has('hotel') ? ' has-error' : '' }}">
            <label for="hotel">@lang('hotels.title'):</label>
            <select class="form-control" title="Elige un hotel" name="hotel" id="hotel" required>
                <option value="{{ id_encode($voucher->hotel->id) }}" selected>{{ $voucher->hotel->business_name }}</option>
            </select>

            @if ($errors->has('hotel'))
                <span class="help-block">
                    <strong>{{ $errors->first('hotel') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group{{ $errors->has('number') ? ' has-error' : '' }}">
            <label for="number">@lang('rooms.title'):</label>
            <select class="form-control" title="{{ trans('rooms.chooseRoom') }}" name="number" id="number" required onchange="getRoomPriceByNumber('{{ id_encode($voucher->hotel->id) }}', this.value)">
                @foreach($rooms as $room)
                    <option value="{{ $room->number }}" {{ $loop->first ? 'selected' : '' }} >{{ $room->number }}</option>
                @endforeach
            </select>

            @if ($errors->has('number'))
                <span class="help-block">
                    <strong>{{ $errors->first('number') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
            <label for="room">@lang('common.price'): <small>@lang('common.tax.title') <span id="tax-value">{{ $rooms->first()->tax * 100 }}</span>%</small></label>
            <input type="number" name="price" id="price" class="form-control" value="{{ round($rooms->first()->price, 0) }}" min="{{ round($rooms->first()->min_price, 0) }}" max="{{ round($rooms->first()->price, 0) }}" required>

            @if ($errors->has('price'))
                <span class="help-block">
                    <strong>{{ $errors->first('price') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group{{ $errors->has('start') ? ' has-error' : '' }}">
            <label for="start">@lang('common.startDate'):</label>
            <input type="string" class="form-control datepicker" name="start" id="start" value="{{ old('start') }}" required>

            @if ($errors->has('start'))
                <span class="help-block">
                    <strong>{{ $errors->first('start') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group{{ $errors->has('end') ? ' has-error' : '' }}">
            <label for="end">@lang('common.endDate'):</label>
            <input type="string" class="form-control datepicker" name="end" id="end" value="{{ old('end') }}">

            @if ($errors->has('end'))
                <span class="help-block">
                    <strong>{{ $errors->first('end') }}</strong>
                </span>
            @endif
        </div>
        <button type="submit" class="btn btn-primary">Agregar</button>
        <a href="{{ route('vouchers.show', ['id' => id_encode($voucher->id)]) }}" class="btn btn-link">Finalizar</a>
    </form>

    @include('partials.spacer', ['size' => 'md'])

@endsection