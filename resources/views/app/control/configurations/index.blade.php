@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('configurations') }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('configurations.title'),
            'url' => route('configurations.index'),
            'options' => [
                [
                    'option' => trans('common.new'),
                    'url' => route('configurations.create'),
                ],
            ]
        ])

        <div class="row">
            <div class="col-md-12">
                @if ($configurations)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">@lang('common.name')</th>
                                    <th scope="col">@lang('common.enabled.at')</th>
                                    <th scope="col">@lang('common.options')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($configurations as $configuration)
                                    <tr>
                                        <th scope="row">{{ $loop->iteration }}</th>
                                        <td>{{ $configuration->name }}</td>
                                        <td>{{ $configuration->getEnabledDate() }}</td>
                                        <td></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <tr>
                        <td colspan="4">@lang('common.noRecords')</td>
                    </tr>
                @endif
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection
