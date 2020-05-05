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
                    'option' => trans('maintenances.maintenance'),
                    'url' => route('assets.maintenance.form', [
                        'id' => id_encode($asset->id)
                    ]),
                ],
                [
                    'type' => 'dropdown',
                    'option' => trans('common.options'),
                    'url' => [
                        [
                            'option' => trans('common.edit'),
                            'url' => route('assets.edit', [
                                'id' => id_encode($asset->id)
                            ]),
                        ],
                        [
                            'type' => 'confirm',
                            'option' => trans('common.delete.item'),
                            'url' => route('assets.destroy', [
                                'id' => id_encode($asset->id)
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

        @include('app.assets.info')

        <div class="row">
            <div class="col-md-12">
                <div class="spacer-xs"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h3>Mantenimientos</h3>
                @include('partials.list', [
                    'data' => $asset->maintenances,
                    'listHeading' => 'app.assets.maintenance-list-heading',
                    'listRow' => 'app.assets.maintenance-list-row'
                ])
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection