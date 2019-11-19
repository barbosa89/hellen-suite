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
    <form class="mt-4" action="{{ route('invoices.guests.change', ['id' => Hashids::encode($invoice->id), 'room' => Hashids::encode($guest->id)]) }}" method="POST" accept-charset="utf-8">
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

        <div class="form-group{{ $errors->has('guest') ? ' has-error' : '' }}">
            <label for="guest">@lang('guests.guest'):</label>
        <input type="text" name="guest" id="guest" class="form-control" value="{{ $guest->full_name }}" readonly>

            @if ($errors->has('guest'))
                <span class="help-block">
                    <strong>{{ $errors->first('guest') }}</strong>
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
            <select class="form-control" title="{{ trans('rooms.chooseRoom') }}" name="number" id="number" required>
                @foreach($invoice->rooms->where('id', '!=', $room->id) as $room)
                    <option value="{{ $room->number }}" {{ $loop->first ? 'selected' : '' }} >{{ $room->number }}</option>
                @endforeach
            </select>

            @if ($errors->has('number'))
                <span class="help-block">
                    <strong>{{ $errors->first('number') }}</strong>
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