@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('guests') }}
@endsection

@section('content')

    <div id="page-wrapper">
        <x-navigation
            title="{{ trans('guests.title') }}"
            url="{{ route('guests.index') }}"
            search>
            @can('guests.index')
                @if ($guests->isNotEmpty())
                    <li class="nav-item">
                        <a href="{{ route('guests.export') }}" class="nav-link">
                            @lang('reports.list')
                        </a>
                    </li>
                @endif
            @endcan

            @can('guests.create')
                <li class="nav-item">
                    <a href="{{ route('guests.create') }}" class="nav-link">
                        @lang('common.new')
                    </a>
                </li>
            @endcan
        </x-navigation>

        <div class="row">
            <div class="col-md-12">
                @include('app.guests.table')
            </div>
        </div>

        <div class="row my-4">
            <div class="col-12">
                {{ $guests->withQueryString()->links() }}
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection
