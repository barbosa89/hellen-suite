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
                        <a href="{{ route('guests.export') }}" class="nav-link">
                            {{ trans('reports.list') }}
                        </a>
                    </li>
                @endcan

                @can('guests.index')
                    <li class="nav-item">
                        <a href="{{ route('guests.create') }}" class="nav-link">
                            {{ trans('common.new') }}
                        </a>
                    </li>
                @endcan
            </ul>
        </x-navigation>

        <guests-index></guests-index>

        @include('partials.modal-confirm')
    </div>

@endsection
