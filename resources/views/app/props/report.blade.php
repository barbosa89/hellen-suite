@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('props') }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('props.title'),
            'url' => route('props.index'),
            'options' => [
                [
                    'option' => trans('common.back'),
                    'url' => route('props.index')
                ],
            ]
        ])

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <h2 class="text-center">@lang('common.report.of') @lang('props.title')</h2>

                <form action="{{ route('props.report.export') }}" method="POST">
                    @csrf()

                    <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
                        <label for="pwd">@lang('reports.type'):</label>
                        <select class="form-control" title="{{ trans('common.chooseOption') }}" name="type" id="type" required>
                            <option value="all" selected>@lang('hotels.all')</option>
                            <option value="one">@lang('hotels.one')</option>
                        </select>

                        @if ($errors->has('type'))
                            <span class="help-block">
                                <strong>{{ $errors->first('type') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('hotel') ? ' has-error' : '' }}" id="hotel-select" style="display:none;">
                        <label for="pwd">@lang('hotels.title'):</label>
                        <select class="form-control" title="{{ trans('hotels.choose') }}" name="hotel" id="hotel">
                            @foreach ($hotels as $hotel)
                                <option value="{{ id_encode($hotel->id) }}">{{ $hotel->business_name }}</option>
                            @endforeach
                        </select>

                        @if ($errors->has('hotel'))
                            <span class="help-block">
                                <strong>{{ $errors->first('hotel') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('start') ? ' has-error' : '' }}">
                        <label for="start">@lang('common.startDate'):</label>
                        <input type="date" class="form-control" name="start" id="start" value="{{ old('start') }}" required>

                        @if ($errors->has('start'))
                            <span class="help-block">
                                <strong>{{ $errors->first('start') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('end') ? ' has-error' : '' }}">
                        <label for="end">@lang('common.endDate'):</label>
                        <input type="date" class="form-control" name="end" id="end" value="{{ old('end') }}" required>

                        @if ($errors->has('end'))
                            <span class="help-block">
                                <strong>{{ $errors->first('end') }}</strong>
                            </span>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-dark">@lang('common.query')</button>
                    <a href="{{ route('props.index') }}" class="btn btn-default">
                        @lang('common.back')
                    </a>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    document.getElementById('type').addEventListener('change', function () {
        const hotelSelect = document.getElementById('hotel-select');

        if (this.value === 'one') {
            if (hotelSelect.style.display === 'none' || hotelSelect.style.display === '') {
                hotelSelect.style.display = 'block'; // Equivalent to fadeIn
            }
        } else {
            if (hotelSelect.style.display === 'block') {
                hotelSelect.style.display = 'none'; // Equivalent to fadeOut
            }
        }
    });
</script>
@endsection