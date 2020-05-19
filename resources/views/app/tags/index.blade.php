@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('tags') }}
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
            <div class="col">
                <h2>Tags</h2>
            </div>
        </div>

        @if ($tags->isNotEmpty())
            <tag-list :tags=@json($tags)></tag-list>
        @else
            @include('partials.no-records')
        @endif
    </div>

@endsection