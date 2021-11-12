@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('configurations') }}
@endsection

@section('content')

    <div id="page-wrapper">
        <x-navigation
            title="{{ trans('configurations.title') }}"
            url="{{ route('configurations.index') }}">
        </x-navigation>

        <div class="row">
            <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">@lang('common.name')</th>
                                    <th scope="col">@lang('common.module')</th>
                                    <th scope="col">@lang('common.enabled.at')</th>
                                    <th scope="col">@lang('common.options')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($configurations->isNotEmpty())
                                    @foreach ($configurations as $configuration)
                                        <tr>
                                            <th scope="row">{{ $loop->iteration }}</th>
                                            <td>{{ $configuration->present()->full_name }}</td>
                                            <td>{{ $configuration->present()->module_name }}</td>
                                            <td>{{ $configuration->present()->enabled_at }}</td>
                                            <td>
                                                <x-dropdown-button>
                                                    <a href="#"
                                                        data-url="{{ route('configurations.toggle', ['configuration' => $configuration->hash]) }}"
                                                        data-method="PUT"
                                                        id="modal-confirm"
                                                        onclick="confirmAction(this, event)"
                                                        class="dropdown-item">
                                                        {{ $configuration->present()->action }}
                                                    </a>
                                                </x-dropdown-button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5">@lang('common.noRecords')</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection
