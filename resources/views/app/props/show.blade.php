@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('prop', $prop) }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('props.title'),
            'url' => route('props.index'),
            'options' => [
                [
                    'option' => 'Transacciones',
                    'url' => route('props.transactions.form')
                ],
                [
                    'option' => trans('common.new'),
                    'url' => route('props.create')
                ],
                [
                    'type' => 'dropdown',
                    'option' => trans('common.options'),
                    'url' => [
                        [
                            'option' => trans('common.edit'),
                            'url' => route('props.edit', [
                                'room' => Hashids::encode($prop->id)
                            ]),
                        ],
                        [
                            'type' => 'confirm',
                            'option' => trans('common.delete'),
                            'url' => route('props.destroy', [
                                'id' => Hashids::encode($prop->id)
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
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                <h2>@lang('common.description'):</h2>
                <p>{{ $prop->description }}</p>
            </div>
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                <h2>@lang('common.quantity'):</h2>
                {{ $prop->quantity }}
            </div>
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                <h3>@lang('common.model'):</h3>
                <p>{{ $prop->hotel->business_name }}</p>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <h3>Transacciones</h3>
            </div>
            <div class="col-md-12">
                @include('partials.list', [
                    'data' => $prop->transactions,
                    'listHeading' => 'app.props.transactions-heading',
                    'listRow' => 'app.props.transactions-row',
                    'where' => null
                ])
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection