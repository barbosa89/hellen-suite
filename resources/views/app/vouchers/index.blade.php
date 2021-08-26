@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('vouchers') }}
@endsection

@section('content')
    <x-navigation
        title="{{ trans('vouchers.title') }}"
        url="{{ route('vouchers.index') }}"
        hotel-select>
        <ul class="navbar-nav ml-auto">
            @can('vouchers.edit')
                <li class="nav-item">
                    <a href="{{ route('vouchers.process') }}" class="nav-link">
                        {{ trans('vouchers.process') }}
                    </a>
                </li>
            @endcan

            @can('vouchers.index')
                <li class="nav-item">
                    <a href="#" data-toggle="modal" data-target="#voucher-filter" class="nav-link">
                        {{ trans('common.filters.filters') }}
                    </a>
                </li>
            @endcan
        </ul>
    </x-navigation>

    <vouchers-index></vouchers-index>

    @include('partials.modal-confirm')
@endsection
