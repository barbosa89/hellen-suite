@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('plans') }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('plans.title'),
            'url' => route('plans.index'),
            'options' => [
                [
                    'option' => trans('common.back'),
                    'url' => url()->previous()
                ],
            ]
        ])

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <h2 class="text-center">@lang('common.creationOf') Plan</h2>
                <form action="{{ route('plans.store') }}" method="POST">
                    @csrf()

                    <div class="form-group{{ $errors->has('price') ? ' has-error' : '' }}">
                        <label for="price">@lang('common.price'): <small>{{ trans('common.required') }}</small></label>
                        <input type="number" class="form-control" name="price" id="price" value="{{ old('price') }}" step="1" min="0" placeholder="{{ trans('common.price') }}" required>

                        @if ($errors->has('price'))
                            <span class="help-block">
                                <strong>{{ $errors->first('price') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('months') ? ' has-error' : '' }}">
                        <label for="months">@lang('plans.duration'): <small>{{ trans('common.required') }}</small></label>
                        <input type="number" class="form-control" name="months" id="months" value="{{ old('months', 2) }}" min="2" max="12" step="1" required>

                        @if ($errors->has('months'))
                            <span class="help-block">
                                <strong>{{ $errors->first('months') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
                        <label for="type">@lang('plans.types'): <small>{{ trans('common.required') }}</small></label>
                        <select class="form-control selectpicker" name="type" id="type" required>
                            @foreach ($types as $type => $description)
                                <option value="{{ $type }}">{{ $description }}</option>
                            @endforeach
                        </select>

                        @if ($errors->has('type'))
                            <span class="help-block">
                                <strong>{{ $errors->first('type') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                        <label for="status">@lang('common.status'): <small>{{ trans('common.required') }}</small></label>
                        <select class="form-control selectpicker" name="status" id="status" required>
                            <option value="1">@lang('common.active')</option>
                            <option value="0">@lang('common.inactive')</option>
                        </select>

                        @if ($errors->has('status'))
                            <span class="help-block">
                                <strong>{{ $errors->first('status') }}</strong>
                            </span>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary">@lang('common.create')</button>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">@lang('common.back')</a>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script type="text/javascript">
        $("#type").on('change', function(e) {
            if (this.value == 'headquarters') {
                if ($('#main-hotel').is(':hidden')) {
                    $('#main-hotel').fadeIn();
                }
            } else {
                if ($('#main-hotel').is(':visible')) {
                    $('#main-hotel').fadeOut();
                }
            }
        });
    </script>
@endsection
