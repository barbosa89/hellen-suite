@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('hotel settings', $hotel) }}
@endsection

@section('content')

    <div id="page-wrapper">
        <x-navigation
            title="{{ trans('settings.title') }}"
            url="{{ route('hotels.settings.index', ['hotel' => $hotel->hash]) }}">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a href="{{ route('hotels.index') }}" class="nav-link">
                        {{ trans('common.back') }}
                    </a>
                </li>
            </ul>
        </x-navigation>

        <h1>Hotel settings</h1>

        @include('partials.modal-confirm')
    </div>

@endsection
