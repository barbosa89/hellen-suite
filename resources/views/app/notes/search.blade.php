@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('notes') }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('notes.title'),
            'url' => route('notes.index'),
            'options' => [
                [
                    'option' => trans('common.back'),
                    'url' => route('notes.index'),
                ],
            ]
        ])

        <div class="row">
            <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                <span class="d-block">Hotel</span>
                <h4>{{ $hotel->business_name }}</h4>
            </div>
            <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                <form action="{{ route('notes.search') }}" method="get">
                    <input type="hidden" name="hotel" value="{{ id_encode($hotel->id) }}">

                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="start">{{ trans('common.startDate') }}:</label>
                                <input type="text" class="form-control datepicker @error('start') is-invalid @enderror" name="start" id="start" value="{{ $start }}" required>

                                @error ('start')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                            <div class="form-group">
                                <label for="end">{{ trans('common.endDate') }}:</label>
                                <input type="text" class="form-control datepicker @error('end') is-invalid @enderror" name="end" id="end" value="{{ $end }}" required>

                                @error ('end')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row my-2">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                            <div class="form-group">
                                <label for="query">{{ trans('common.search') }}:</label>
                                <input type="text" class="form-control @error('query') is-invalid @enderror" name="query" id="query" value="{{ $text ?? '' }}" placeholder="{{ trans('common.optional') }}">

                                @error ('query')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" role="button" class="btn btn-primary">
                                {{ trans('common.query') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row my-4">
            <div class="col-12">
                {{ trans('notes.title') }}
            </div>
        </div>

        @if ($notes->isNotEmpty())
            @foreach ($notes as $note)
                @include('app.notes.note')
            @endforeach
        @else
            <div class="row my-4">
                <div class="col-12">
                    <div class="alert alert-info">
                        {{ trans('common.without.results') }}
                    </div>
                </div>
            </div>
        @endif

        <div class="row my-4">
            <div class="col-12">
                {{ $notes->withQueryString()->links() }}
            </div>
        </div>
    </div>

@endsection