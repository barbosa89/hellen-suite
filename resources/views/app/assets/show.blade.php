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
                    'option' => trans('maintenances.actions.create'),
                    'url' => route('assets.maintenances.create', [
                        'asset' => $asset->hash
                    ]),
                ],
                [
                    'type' => 'dropdown',
                    'option' => trans('common.options'),
                    'url' => [
                        [
                            'option' => trans('common.edit'),
                            'url' => route('assets.edit', [
                                'id' => $asset->hash
                            ]),
                        ],
                        [
                            'type' => 'confirm',
                            'option' => trans('common.delete.item'),
                            'url' => route('assets.destroy', [
                                'id' => $asset->hash
                            ]),
                            'method' => 'DELETE'
                        ],
                    ]
                ],
                [
                    'option' => trans('common.back'),
                    'url' => route('assets.index')
                ],
            ]
        ])

        @include('app.assets.info')

        <div class="row">
            <div class="col-md-12">
                <div class="spacer-xs"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h3>
                    @lang('maintenances.title')
                </h3>
                @include('partials.list', [
                    'data' => $asset->maintenances,
                    'listHeading' => 'app.assets.maintenances.list-heading',
                    'listRow' => 'app.assets.maintenances.list-row'
                ])
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection
