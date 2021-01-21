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
                'option' => trans('vouchers.back'),
                'url' => route('vouchers.show', [
                    'id' => id_encode($voucher->id)
                ])
            ]
        ]
    ])

    @include('app.vouchers.info')

    <h3 class="text-center">@lang('rooms.changeRoom')</h3>
    <form class="mt-4" action="{{ route('vouchers.guests.change', ['id' => id_encode($voucher->id), 'guest' => id_encode($guest->id)]) }}" method="POST" accept-charset="utf-8">
        @csrf
        <div class="form-group{{ $errors->has('hotel') ? ' has-error' : '' }}">
            <label for="hotel">Hotel:</label>
            <select class="form-control" title="Elige un hotel" name="hotel" id="hotel" readonly>
                <option value="{{ id_encode($voucher->hotel->id) }}" selected>{{ $voucher->hotel->business_name }}</option>
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
                @foreach($voucher->rooms->where('id', '!=', $room->id) as $room)
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
        <a href="{{ route('vouchers.show', ['id' => id_encode($voucher->id)]) }}" class="btn btn-link">
            @lang('common.back')
        </a>
    </form>

    @include('partials.spacer', ['size' => 'md'])

@endsection
