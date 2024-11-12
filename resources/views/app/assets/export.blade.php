@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('assets') }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('assets.title'),
            'url' => route('assets.index'),
            'options' => [
                [
                    'option' => trans('common.back'),
                    'url' => route('assets.index')
                ],
            ]
        ])

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <h2 class="text-center">@lang('reports.listOf') @lang('assets.title')</h2>

                <form action="{{ route('assets.export') }}" method="POST">
                    @csrf()

                    <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
                        <label for="pwd">@lang('common.chooseOption'):</label>
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

                    <button type="submit" class="btn btn-dark">@lang('common.query')</button>
                    <a href="{{ route('assets.index') }}" class="btn btn-default">
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
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('type').addEventListener('change', function () {
            const hotelSelect = document.getElementById('hotel-select')

            if (this.value === 'one') {
                if (hotelSelect.style.display === 'none' || hotelSelect.style.display === '') {
                    hotelSelect.style.display = 'block' // Equivalent to fadeIn
                }
            } else {
                if (hotelSelect.style.display === 'block') {
                    hotelSelect.style.display = 'none' // Equivalent to fadeOut
                }
            }
        })
    })
</script>
@endsection
