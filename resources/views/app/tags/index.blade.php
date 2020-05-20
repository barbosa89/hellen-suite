@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('tags') }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => 'Tags',
            'url' => route('tags.index'),
            'options' => []
        ])

        @if ($tags->isNotEmpty())
            <tag-list :tags=@json($tags)></tag-list>
        @else
            @include('partials.no-records')
        @endif
    </div>

@endsection