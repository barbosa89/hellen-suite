@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('guest', $guest) }}
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

        <div class="row">
            <div class="col-md-12">
                @include('partials.form', [
                    'title' => [
                        'title' => trans('common.actions.edit', ['model' => trans('guests.guest')]),
                        'align' => 'text-center'
                    ],
                    'url' => route('guests.update', ['id' => $guest->hash]),
                    'method' => 'PUT',
                    'fields' => [
                        'app.guests.edit-fields',
                    ],
                    'btn' => trans('common.update'),
                    'link' => [
                        'name' => trans('common.back'),
                        'href' => url()->previous()
                    ]
                ])
            </div>
        </div>

        @include('partials.spacer', ['size' => 'md'])
    </div>

@endsection
