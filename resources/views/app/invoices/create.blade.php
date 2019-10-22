@extends('layouts.panel')

@section('content')

    @include('partials.page-header', [
        'title' => trans('invoices.title'),
        'url' => route('invoices.index'),
        'options' => [
            [
                'option' => trans('common.back'),
                'url' => url()->previous()
            ],
        ]
    ])

    <h3 class="text-center">Agregar habitaciones</h3>
    <form class="mt-4" action="{{ route('invoices.store') }}" method="POST" accept-charset="utf-8">
        @csrf
        <input type="hidden" name="hotel" value="{{ Hashids::encode($hotel->id) }}" required>

        <div class="form-group{{ $errors->has('hotel') ? ' has-error' : '' }}">
            <label for="hotel">@lang('hotels.title'):</label>
            <input type="text" class="form-control" name="headquarter" id="headquarter" readonly value="{{ $hotel->business_name }}">

            @if ($errors->has('hotel'))
                <span class="help-block">
                    <strong>{{ $errors->first('hotel') }}</strong>
                </span>
            @endif
        </div>

        @foreach ($hotel->rooms as $room)
            <div class="row mt-4">
                <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                    <div class="form-group{{ $errors->has('number') ? ' has-error' : '' }}">
                        @if ($loop->first)
                            <label for="number">Habitación:</label>
                        @endif
                        <input type="text" name="room[{{ $loop->index }}][number]" class="form-control" value="{{ $room->number }}" required readonly>

                        @if ($errors->has('number'))
                            <span class="help-block">
                                <strong>{{ $errors->first('number') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                    <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
                        @if ($loop->first)
                            <label for="room">Precio:</label>
                        @endif
                        <input type="number" name="room[{{ $loop->index }}][price]" class="form-control" value="{{ round($room->price, 0) }}" required min="{{ $room->min_price }}" max="{{ $room->price }}">

                        @if ($errors->has('room.' . $loop->index. '.price'))
                            <span class="help-block">
                                <strong>{{ $errors->first('room.' . $loop->index. '.price') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                    <div class="form-group{{ $errors->has('room.' . $loop->index . '.start') ? ' has-error' : '' }}">
                        @if ($loop->first)
                            <label for="start">@lang('common.startDate'):</label>
                        @endif
                        <input type="string" class="form-control datepicker date-start" name="room[{{ $loop->index }}][start]" value="{{ old('room.' . $loop->index . '.start') }}" required>

                        @if ($errors->has('room.' . $loop->index. '.start'))
                            <span class="help-block">
                                <strong>{{ $errors->first('room.' . $loop->index. '.start') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                    <div class="form-group{{ $errors->has('room.' . $loop->index . '.end') ? ' has-error' : '' }}">
                        @if ($loop->first)
                            <label for="start">@lang('common.endDate'):</label>
                        @endif
                        <input type="string" class="form-control datepicker" name="room[{{ $loop->index }}][end]" value="{{ old('room.' . $loop->index . '.end') }}">

                        @if ($errors->has('room.' . $loop->index . '.end'))
                            <span class="help-block">
                                <strong>{{ $errors->first('room.' . $loop->index . '.end') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

        <div class="form-group{{ $errors->has('registry') ? ' has-error' : '' }} mt-4">
            <label for="start">Registro:</label>
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

        @include('partials.spacer', ['size' => 'md'])

        <button type="submit" class="btn btn-primary">Agregar</button>
        <a href="{{ route('rooms.index') }}" class="btn btn-link">Cancelar</a>
    </form>

    @include('partials.spacer', ['size' => 'md'])

@endsection

@section('scripts')
    <script>
        $(".date-start").each(function (index, item) {
            var date = new Date()
            var year = date.getFullYear()

            if (date.getMonth() > 8) {
                var month = date.getMonth() + 1
            } else {
                var month = '0' + date.getMonth()
            }

            if (date.getDate() > 8) {
                var day = date.getDate() + 1
            } else {
                var day = '0' + date.getDate()
            }

            item.setAttribute('value', year + '-' + month + '-' + day)
        });
    </script>
@endsection