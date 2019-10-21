@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('rooms') }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('rooms.title'),
            'url' => route('rooms.index'),
            'options' => [
                [
                    'type' => 'hideable',
                    'show' => auth()->user()->can('rooms.create'),
                    'option' => trans('common.new'),
                    'url' => route('rooms.create')
                ],
            ],
            'search' => [
                'action' => route('rooms.search')
            ]
        ])

        <room-list :hotels="{{ $hotels->toJson() }}"></room-list>

        @include('partials.modal-confirm')
    </div>

@endsection