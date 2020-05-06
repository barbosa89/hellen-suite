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

        <note-create :hotels='@json($hotels)' :tags='@json($tags)'></note-create>
    </div>

@endsection