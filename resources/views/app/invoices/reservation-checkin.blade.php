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
                'option' => trans('common.back'),
                'url' => route('rooms.index')
            ],
        ]
    ])

    @include('app.invoices.info')

    <h3 class="text-center">{{ trans('common.register') }} {{ trans('invoices.route') }}</h3>

    <form class="mt-4" action="{{ route('invoices.reservation.checkin.store', ['id' => Hashids::encode($invoice->id)]) }}" method="POST" accept-charset="utf-8">
        @csrf

        <div class="form-group{{ $errors->has('origin') ? ' has-error' : '' }}">
            <label for="origin">Origen:</label>
            <input type="text" class="form-control" name="origin" id="origin" value="{{ old('origin') }}" required>

            @if ($errors->has('origin'))
                <span class="help-block">
                    <strong>{{ $errors->first('origin') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group{{ $errors->has('destination') ? ' has-error' : '' }}">
            <label for="destination">Destino:</label>
            <input type="text" class="form-control" name="destination" id="destination" value="{{ old('destination') }}" required>

            @if ($errors->has('destination'))
                <span class="help-block">
                    <strong>{{ $errors->first('destination') }}</strong>
                </span>
            @endif
        </div>

        <button type="submit" class="btn btn-primary">@lang('common.register')</button>
        <a href="{{ route('rooms.index') }}" class="btn btn-link">
            @lang('common.cancel')
        </a>
    </form>

    @include('partials.spacer', ['size' => 'md'])

@endsection