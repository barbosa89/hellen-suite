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
                <h2>Hotel:</h2>
                <p>
                    <a href="{{ route('hotels.show', ['id' => Hashids::encode($asset->hotel->id)]) }}">
                        {{ $asset->hotel->business_name }}
                    </a>
                </p>
            </div>
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
        </div>

        <div class="row">
            <div class="col-xs-4 col-sm-3 col-md-3 col-lg-3">
                <h3>@lang('common.model'):</h3>
                <p>{{ $asset->model ?? trans('common.noData') }}</p>
            </div>
            <div class="col-xs-4 col-sm-3 col-md-3 col-lg-3">
                <h3>@lang('common.reference'):</h3>
                <p>{{ $asset->reference ?? trans('common.noData') }}</p>
            </div>
            <div class="col-xs-4 col-sm-3 col-md-3 col-lg-3">
                <h3>@lang('common.location'):</h3>
                <p>{{ $asset->location ?? trans('common.noData') }}</p>
            </div>
            <div class="col-xs-4 col-sm-3 col-md-3 col-lg-3">
                <h3>@lang('rooms.room'):</h3>
                @if($asset->room)
                    <a href="{{ route('rooms.show', ['room' => Hashids::encode($asset->room->id)]) }}">{{ $asset->room->number }}</a>
                @else
                    {{ trans('common.noData') }}
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="spacer-xs"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h3>Mantenimientos</h3>

            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection