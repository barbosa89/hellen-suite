@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('replicate prop') }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('props.title'),
            'url' => route('props.index'),
            'options' => [
                [
                    'option' => trans('common.back'),
                    'url' => url()->previous()
                ],
            ]
        ])

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <h2 class="text-center">Replicación de @lang('props.title')</h2>
                <form action="{{ route('props.replicate.replicants') }}" method="POST">
                    @csrf()

                    <div class="form-group{{ $errors->has('from') ? ' has-error' : '' }}">
                        <label for="pwd">Replicar desde el hotel:</label>
                        <select class="form-control selectpicker" title="Elige un hotel o sede" name="from" id="from" required>
                            @foreach ($hotels as $hotel)
                                <option value="{{ id_encode($hotel->id) }}" {{ $loop->first ? 'selected' : '' }}>{{ $hotel->business_name }}</option>
                            @endforeach
                        </select>

                        @if ($errors->has('from'))
                            <span class="help-block">
                                <strong>{{ $errors->first('from') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('to') ? ' has-error' : '' }}" id="to-list">
                        <label for="pwd">Para el hotel:</label>
                        <select class="form-control selectpicker" title="Elige un hotel o sede" name="to" id="to" required>
                            @if ($hotels->count() > 1)
                                @foreach ($hotels->where('id', '!=', $hotels->first()->id) as $hotel)
                                    <option value="{{ id_encode($hotel->id) }}" {{ $loop->first ? 'selected' : '' }}>{{ $hotel->business_name }}</option>
                                @endforeach
                            @endif
                        </select>

                        @if ($errors->has('to'))
                            <span class="help-block">
                                <strong>{{ $errors->first('to') }}</strong>
                            </span>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary">Replicar</button>
                    <a href="{{ route('props.index') }}" class="btn btn-default">
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

@section('scripts')
    <script>
        $('#from').change(function () {
            if (this.value != null || this.value != "") {
                $.ajax({
                    type: 'POST',
                    url: '/hotels/different',
                    data: {
                        hotel: this.value
                    },
                    success: function (result) {
                        var hotels = JSON.parse(result.hotels);

                        if (hotels.length) {
                            if ($("#to-list").is(':hidden')) {
                                $("#to-list").fadeIn();
                            }

                            var newOptions = [];
                            hotels.forEach(function(hotel) {
                                newOptions.push("<option value=" + hotel.hash + ">" + hotel.business_name + "</option>");
                            });

                            $("#to").html(newOptions);
                            $("#to").selectpicker('refresh');
                        } else {
                            toastr.info(
                                'No hay hoteles para replicar',
                                'Sin registros'
                            );

                            $('#to').val('');

                            if ($("#to-list").is(':visible')) {
                                $("#to-list").fadeOut();
                            }
                        }
                    },
                    error: function(xhr) {
                        toastr.error(
                            'Ha ocurrido un error',
                            'Error'
                        );
                    }
                })
            }
        });
    </script>
@endsection