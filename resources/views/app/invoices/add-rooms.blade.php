@extends('layouts.panel')

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
                'type' => 'hideable',
                'option' => trans('invoices.registerCompany'),
                'url' => route('invoices.companies.search', [
                    'id' => Hashids::encode($invoice->id)
                ]),
                'show' => $invoice->for_company
            ],
            [
                'option' => trans('invoices.see'),
                'url' => route('invoices.show', [
                    'id' => Hashids::encode($invoice->id)
                ])
            ],
            [
                'option' => trans('common.back'),
                'url' => url()->previous()
            ],
        ]
    ])

    @include('app.invoices.info')

    <h3 class="text-center">Agregar habitación</h3>
    <form class="mt-4" action="{{ route('invoices.rooms.add', ['id' => Hashids::encode($invoice->id)]) }}" method="POST" accept-charset="utf-8">
        @csrf
        <div class="form-group{{ $errors->has('hotel') ? ' has-error' : '' }}">
            <label for="hotel">@lang('hotels.title'):</label>
            <select class="form-control" title="Elige un hotel" name="hotel" id="hotel" required>
                <option value="{{ Hashids::encode($hotel->id) }}" selected>{{ $hotel->business_name }}</option>
                @foreach($hotels->where('id', '!=', $hotel->id) as $headquarter)
                    <option value="{{ Hashids::encode($headquarter->id) }}">{{ $headquarter->business_name }}</option>
                @endforeach
            </select>

            @if ($errors->has('hotel'))
                <span class="help-block">
                    <strong>{{ $errors->first('hotel') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group{{ $errors->has('room') ? ' has-error' : '' }}">
            <label for="room">@lang('rooms.title'):</label>
            <select class="form-control" title="{{ trans('rooms.chooseRoom') }}" name="room" id="room" required>
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
        <a href="{{ route('invoices.show', ['id' => Hashids::encode($invoice->id)]) }}" class="btn btn-link">Finalizar</a>
    </form>

    @include('partials.spacer', ['size' => 'md'])

@endsection

@section('scripts')
    <script>
        $("#hotel").change(function() {
            $.ajax({
                type: 'POST',
                url: '/rooms/list',
                data: {
                    hotel: this.value
                },
                success: function(result) {
                    if (Object.keys(JSON.parse(result.rooms)).length > 0) {
                        var select = $("#room");

                        if (!select.is(":visible")) {
                            $("#room").parent().fadeIn();
                        }
                        select.empty();

                        $.each(JSON.parse(result.rooms), function(key, value) {
                            select.append($("<option></option>")
                                .attr("value", value.hash).text(value.number));
                        });
                    } else {
                        $("#room").parent().fadeOut();

                        toastr.info(
                            'No hay habitaciones disponibles en el hotel seleccionado',
                            'Lleno total'
                        );
                    }
                },
                error: function(xhr){
                    toastr.error(
                        'Ha ocurrido un error',
                        'Error'
                    );
                }
            })
        });
    </script>
@endsection