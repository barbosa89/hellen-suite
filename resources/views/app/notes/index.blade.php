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
                    'option' => trans('common.create'),
                    'url' => route('notes.create'),
                ],
            ]
        ])

        <form action="{{ route('notes.search') }}" method="get">
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <label for="hotel">Hotel:</label>

                        <select name="hotel" id="hotel" class="form-control @error('hotel') is-invalid @enderror" required>
                            @foreach ($hotels as $hotel)
                                <option value="{{ id_encode($hotel->id) }} ">
                                    {{ $hotel->business_name }}
                                </option>
                            @endforeach
                        </select>

                        @error ('hotel')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row my-2">
                <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                    <div class="form-group">
                        <label for="start">{{ trans('common.startDate') }}:</label>
                        <input type="text" class="form-control datepicker @error('start') is-invalid @enderror" name="start" id="start" value="{{ old('start') }}" required>

                        @error ('start')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                    <div class="form-group">
                        <label for="end">{{ trans('common.endDate') }}:</label>
                        <input type="text" class="form-control datepicker @error('end') is-invalid @enderror" name="end" id="end" value="{{ old('end') }}" required>

                        @error ('end')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row my-4">
                <div class="col-12">
                    <button type="submit" role="button" class="btn btn-primary">
                        {{ trans('common.query') }}
                    </button>
                </div>
            </div>
        </form>
    </div>

@endsection