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
                <div class="row my-4">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12 mt-2">
                                <p class="text-muted border-top">
                                    <small>{{ trans('common.created.at') }}: {{ $note->created_at }}</small>
                                </p>
                            </div>
                            <div class="col-12">
                                <p>
                                    {{ $note->content }}
                                </p>
                            </div>
                            <div class="col-12">
                                <span>
                                    <b>{{ $note->team_member_name }}</b>
                                </span>
                                <span class="d-block">
                                    <small>{{ $note->team_member_email }}</small>
                                </span>
                            </div>
                            <div class="col-12 mt-2">
                                <span><b>Tags</b>:</span>
                                @foreach ($note->tags as $tag)
                                    <a href="{{ route('tags.show', ['id' => id_encode($tag->id)]) }}">#{{ $tag->slug }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
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