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
                        <select class="form-control" title="Elige un hotel o sede" name="from" id="from" required>
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
                        <select class="form-control" title="Elige un hotel o sede" name="to" id="to" required>
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
import { toast } from 'vue3-toastify'
import { trans } from 'laravel-vue-i18n'

document.querySelector('#from').addEventListener('change', function () {
    if (this.value !== null && this.value !== "") {
        axios.post('/hotels/different', {
            hotel: this.value
        })
        .then(function (response) {
            var hotels = JSON.parse(response.data.hotels);

            if (hotels.length) {
                var toList = document.querySelector("#to-list");
                if (toList.style.display === 'none') {
                    toList.style.display = 'block';
                }

                var newOptions = hotels.map(function(hotel) {
                    return "<option value='" + hotel.hash + "'>" + hotel.business_name + "</option>";
                }).join('');

                document.querySelector("#to").innerHTML = newOptions;
            } else {
                toast.info('No hay hoteles para replicar'); // TODO: Add translation
                document.querySelector('#to').value = '';

                if (toList.style.display === 'block') {
                    toList.style.display = 'none';
                }
            }
        })
        .catch(function (error) {
            toast.error('Ha ocurrido un error'); // TODO: Add translation
        });
    }
});
</script>
@endsection