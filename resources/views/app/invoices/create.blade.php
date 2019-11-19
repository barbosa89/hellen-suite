@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('invoices') }}
@endsection

@section('content')

    @include('partials.page-header', [
        'title' => trans('invoices.title'),
        'url' => route('invoices.index'),
        'options' => [
            [
                'option' => trans('common.back'),
                'url' => route('rooms.index')
            ],
        ]
    ])

    <h3 class="text-center">Agregar habitaciones</h3>

    <div class="row mt-4 mb-4">
        <div class="col-xs-6 col-sm-6 col-md-6 col-md-6">
            <span>Hotel:</span>
            <h4>{{ $hotel->business_name }}</h4>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 col-md-6">
            <span>NIT:</span>
            <h4>{{ $hotel->tin }}</h4>
        </div>
    </div>

    <form class="mt-4" action="{{ route('invoices.store') }}" method="POST" accept-charset="utf-8">
        @csrf
        <input type="hidden" name="hotel" value="{{ Hashids::encode($hotel->id) }}" required>

        @foreach ($hotel->rooms as $room)
            <div class="row mt-4">
                <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                    <div class="form-group{{ $errors->has('number') ? ' has-error' : '' }}">
                        @if ($loop->first)
                            <label for="number">Habitación:</label>
                        @endif

                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-door-open"></i>
                                </div>
                            </div>
                            <input type="text" name="room[{{ $loop->index }}][number]" class="form-control" value="{{ $room->number }}" required readonly>
                        </div>

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

                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                            </div>
                            <input type="number" name="room[{{ $loop->index }}][price]" class="form-control" value="{{ round($room->price, 0) }}" required min="{{ $room->min_price }}" max="{{ $room->price }}">
                            <div class="input-group-append">
                                <span class="input-group-text">{{ $room->tax * 100 }}%</span>
                            </div>
                        </div>

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

                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-calendar-plus"></i>
                                </div>
                            </div>
                            <input type="string" class="form-control datepicker start-date" name="room[{{ $loop->index }}][start]" value="{{ old('room.' . $loop->index . '.start') }}" required>
                        </div>

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

                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="fas fa-calendar-times"></i>
                                </div>
                            </div>
                            <input type="string" class="form-control datepicker{{ !$loop->first ? ' end-date' : '' }}" name="room[{{ $loop->index }}][end]" {{ $loop->first ? 'id=common-date' : '' }} value="{{ old('room.' . $loop->index . '.end') }}" placeholder="Campo no obligatorio">
                        </div>

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

        <div id="route" style="display:none;">
            <div class="form-group{{ $errors->has('origin') ? ' has-error' : '' }}">
                <label for="origin">Origen:</label>
                <input type="text" class="form-control" name="origin" id="origin" value="{{ old('origin') }}">

                @if ($errors->has('origin'))
                    <span class="help-block">
                        <strong>{{ $errors->first('origin') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('destination') ? ' has-error' : '' }}">
                <label for="destination">Destino:</label>
                <input type="text" class="form-control" name="destination" id="destination" value="{{ old('destination') }}">

                @if ($errors->has('destination'))
                    <span class="help-block">
                        <strong>{{ $errors->first('destination') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Agregar</button>
        <a href="{{ route('rooms.index') }}" class="btn btn-link">Cancelar</a>
    </form>

    @include('partials.spacer', ['size' => 'md'])

@endsection

@section('scripts')
    <script>
        $("#registry").change(function () {
            if (this.value == 'checkin') {
                $("#route:hidden").fadeIn();
            } else {
                $("#route:visible").fadeOut();
            }
        })

        $(".start-date").each(function (index, item) {
            var date = new Date()
            var year = date.getFullYear()

            if (date.getMonth() > 8) {
                var month = date.getMonth() + 1
            } else {
                var month = '0' + date.getMonth()
            }

            if (date.getDate() > 8) {
                var day = date.getDate()
            } else {
                var day = '0' + date.getDate()
            }

            item.setAttribute('value', year + '-' + month + '-' + day)
        });

        $("#common-date").change(function () {
            $(".end-date").each((index, item) => {
                item.setAttribute('value', this.value)
            })
        })
    </script>
@endsection