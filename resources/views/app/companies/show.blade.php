@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('company', $company) }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('companies.title'),
            'url' => route('companies.index'),
            'options' => [
                [
                    'type' => 'dropdown',
                    'option' => trans('common.options'),
                    'url' => [
                        [
                            'option' => trans('common.edit'),
                            'url' => route('companies.edit', [
                                'id' => Hashids::encode($company->id)
                            ]),
                        ],
                        [
                            'type' => 'confirm',
                            'option' => trans('common.delete.item'),
                            'url' => route('companies.destroy', [
                                'id' => Hashids::encode($company->id)
                            ]),
                            'method' => 'DELETE'
                        ],
                    ]
                ],
                [
                    'option' => trans('common.new'),
                    'url' => route('companies.create')
                ],
                [
                    'option' => trans('common.back'),
                    'url' => route('companies.index')
                ],
            ]
        ])

        @include('app.companies.info')

        <div class="row">
            <div class="col-md-12">
                @include('partials.list', [
                    'data' => $vouchers,
                    'listHeading' => 'app.companies.vouchers.list-heading',
                    'listRow' => 'app.companies.vouchers.list-row'
                ])
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection
