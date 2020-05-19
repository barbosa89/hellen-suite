@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('tag', $tag) }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => 'Tags',
            'url' => route('tags.index'),
            'options' => [
                [
                    'option' => trans('common.back'),
                    'url' => route('tags.index'),
                ],
            ]
        ])

        <div class="row">
            <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                <span class="d-block">Hotel</span>
                <h4>{{ $hotel->business_name }}</h4>
            </div>
            <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                <span class="d-block">Tag</span>
                <h4>{{ $tag->description }}</h4>
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
                                    {!! $note->content !!}
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