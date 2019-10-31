@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('asset', $asset) }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('assets.title'),
            'url' => route('assets.index'),
            'options' => [
                [
                    'type' => 'dropdown',
                    'option' => trans('common.options'),
                    'url' => [
                        [
                            'option' => trans('common.edit'),
                            'url' => route('assets.edit', [
                                'room' => Hashids::encode($asset->id)
                            ]),
                        ],
                        [
                            'type' => 'confirm',
                            'option' => trans('common.delete'),
                            'url' => route('assets.destroy', [
                                'id' => Hashids::encode($asset->id)
                            ]),
                            'method' => 'DELETE'
                        ],
                    ]
                ],
                [
                    'option' => trans('common.back'),
                    'url' => url()->previous()
                ],
            ]
        ])

        <div class="row">
            <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                <h2>@lang('common.description'):</h2>
                <p>{{ $asset->description }}</p>
            </div>
            <div class="col-xs-4 col-sm-3 col-md-3 col-lg-3">
                <h2>@lang('common.brand'):</h2>
                {{ $asset->brand ?? trans('common.noData') }}
            </div>
            <div class="col-xs-4 col-sm-3 col-md-3 col-lg-3">
                <h3>@lang('common.number'):</h3>
                <p>{{ $asset->number }}</p>
            </div>
            <div class="col-xs-4 col-sm-3 col-md-3 col-lg-3">
                <h3>@lang('common.model'):</h3>
                <p>{{ $asset->model ?? trans('common.noData') }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-4 col-sm-3 col-md-3 col-lg-3">
                <h3>@lang('common.reference'):</h3>
                <p>{{ $asset->reference ?? trans('common.noData') }}</p>
            </div>
            <div class="col-xs-4 col-sm-3 col-md-3 col-lg-3">
                <h3>@lang('common.location'):</h3>
                <p>{{ $asset->location ?? trans('common.noData') }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="spacer-xs"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h3>@lang('assets.assignedRoom')</h3>

                <div class="crud-list">
                    <div class="crud-list-heading">
                        <div class="row">
                            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                <h5>@lang('common.number')</h5>
                            </div>
                            <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
                                <h5>@lang('common.description')</h5>
                            </div>
                        </div>
                    </div>
                    <div class="crud-list-items">
                        @if(!empty($asset->room))
                            <div class="crud-list-row">
                                <div class="row">
                                    <div class="col-xs-3 col-sm-4 col-md-4 col-lg-4">
                                        <p><a href="{{ route('rooms.show', ['room' => Hashids::encode($asset->id)]) }}">{{ $asset->number }}</a></p>
                                    </div>
                                    <div class="col-xs-9 col-sm-8 col-md-8 col-lg-8">
                                        <p><a href="{{ route('rooms.show', ['room' => Hashids::encode($asset->id)]) }}">{{ $asset->description }}</a></p>
                                    </div>
                                </div>
                            </div>
                        @else
                            @include('partials.no-records')
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection