@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('guests') }}
@endsection

@section('content')

    <div id="page-wrapper">
        <x-navigation
            title="{{ trans('guests.title') }}"
            url="{{ route('guests.index') }}">
            <ul class="navbar-nav ml-auto">
                @can('guests.index')
                    <li class="nav-item">
                        <a href="{{ route('guests.index') }}" class="nav-link">
                            @lang('common.back')
                        </a>
                    </li>
                @endcan
            </ul>
        </x-navigation>

        <div class="row mb-4">
            <div class="col-md-12">
                @include('partials.form', [
                    'title' => [
                        'title' => trans('common.actions.create', ['model' => trans('guests.guest')]),
                        'align' => 'text-center'
                    ],
                    'url' => route('guests.store'),
                    'fields' => [
                        'app.guests.create-fields',
                    ],
                    'btn' => trans('common.create'),
                    'link' => [
                        'name' => trans('common.back'),
                        'href' => route('guests.index'),
                    ]
                ])
            </div>
        </div>
    </div>
@endsection
