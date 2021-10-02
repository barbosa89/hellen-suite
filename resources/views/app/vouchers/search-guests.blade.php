@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('voucher', $voucher) }}
@endsection

@section('content')

    <div id="page-wrapper">
        <x-navigation
            title="{{ trans('vouchers.title') }}"
            url="{{ route('vouchers.index') }}">
            <ul class="navbar-nav ml-auto">
                @can('vouchers.edit')
                    <li class="nav-item">
                        <a href="{{ route('vouchers.companies.search', ['id' => $voucher->hash]) }}" class="nav-link">
                            @lang('vouchers.link.company')
                        </a>
                    </li>
                @endcan

                @can('guests.create')
                    <a href="#" role="button" data-toggle="modal" data-target="#guest-creation-modal" class="nav-link">
                        @lang('common.actions.create', ['model' => trans('guests.guest')])
                    </a>
                @endcan

                @can('vouchers.show')
                    <li class="nav-item">
                        <a href="{{ route('vouchers.show', ['id' => $voucher->hash]) }}" class="nav-link">
                            @lang('vouchers.back')
                        </a>
                    </li>
                @endcan
            </ul>
        </x-navigation>

        @include('app.vouchers.info')

        <h1 class="text-center mb-4">@lang('common.search') @lang('guests.title')</h1>

        <search-guests
            voucher-hash="{{ $voucher->hash }}"
            :genders='@json($genders)'>
        </search-guests>
    </div>

@endsection
