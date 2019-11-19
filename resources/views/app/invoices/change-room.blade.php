@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('invoice', $invoice) }}
@endsection

@section('content')

    @include('partials.page-header', [
        'title' => trans('invoices.title'),
        'url' => route('invoices.index'),
        'options' => [
            [
                'option' => trans('invoices.registerGuests'),
                'url' => route('invoices.guests.search', ['id' => Hashids::encode($invoice->id)])
            ],
            [
                'option' => $invoice->company ? trans('invoices.linkNewCompany') : trans('invoices.linkCompany'),
                'url' => route('invoices.companies.search', [
                    'id' => Hashids::encode($invoice->id)
                ]),
            ],
            [
                'option' => 'Volver al recibo',
                'url' => route('invoices.show', [
                    'id' => Hashids::encode($invoice->id)
                ])
            ]
        ]
    ])

    @include('app.invoices.info')

    <h3 class="text-center">@lang('rooms.changeRoom')</h3>
    <form class="mt-4" action="{{ route('invoices.rooms.change', ['id' => Hashids::encode($invoice->id), 'room' => Hashids::encode($room->id)]) }}" method="POST" accept-charset="utf-8">
        @csrf
        <div class="form-group{{ $errors->has('hotel') ? ' has-error' : '' }}">
            <label for="hotel">Hotel:</label>
            <select class="form-control" title="Elige un hotel" name="hotel" id="hotel" readonly>
                <option value="{{ Hashids::encode($invoice->hotel->id) }}" selected>{{ $invoice->hotel->business_name }}</option>
            </select>

            @if ($errors->has('hotel'))
                <span class="help-block">
                    <strong>{{ $errors->first('hotel') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group{{ $errors->has('current') ? ' has-error' : '' }}">
            <label for="current">@lang('rooms.currentRoom'):</label>
            <select class="form-control" title="{{ trans('rooms.chooseRoom') }}" name="current" id="current" readonly>
                <option value="{{ $room->number }}" selected>{{ $room->number }}</option>
            </select>

            @if ($errors->has('current'))
                <span class="help-block">
                    <strong>{{ $errors->first('current') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group{{ $errors->has('number') ? ' has-error' : '' }}">
            <label for="number">@lang('rooms.availableRooms'):</label>
            <select class="form-control" title="{{ trans('rooms.chooseRoom') }}" name="number" id="number" required onchange="getRoomPriceByNumber('{{ Hashids::encode($invoice->hotel->id) }}', this.value)">
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
            <label for="room">@lang('common.price'): <small>@lang('common.tax') <span id="tax-value">{{ $rooms->first()->tax * 100 }}</span>%</small></label>
            <input type="number" name="price" id="price" class="form-control" value="{{ round($rooms->first()->price, 0) }}" min="{{ round($rooms->first()->min_price, 0) }}" max="{{ round($rooms->first()->price, 0) }}" required>

            @if ($errors->has('price'))
                <span class="help-block">
                    <strong>{{ $errors->first('price') }}</strong>
                </span>
            @endif
        </div>

        <button type="submit" class="btn btn-primary">
            @lang('common.change')
        </button>
        <a href="{{ route('invoices.show', ['id' => Hashids::encode($invoice->id)]) }}" class="btn btn-link">
            @lang('common.finalize')
        </a>
    </form>

    @include('partials.spacer', ['size' => 'md'])

@endsection