@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('vehicle', $vehicle) }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('vehicles.title'),
            'url' => route('vehicles.index'),
            'options' => [
                [
                    'option' => trans('common.back'),
                    'url' => route('vehicles.index')
                ],
            ]
        ])

        <div class="row">
            <div class="col-md-12">
                @include('partials.form', [
                    'title' => [
                        'title' => trans('common.editionOf') . ' ' . trans('vehicles.title'),
                        'align' => 'text-center'
                    ],
                    'url' => route('vehicles.update', [
                        'id' => Hashids::encode($vehicle->id)
                    ]),
                    'method' => 'PUT',
                    'fields' => [
                        'app.vehicles.edit-fields',
                    ],
                    'btn' => trans('common.update'),
                    'link' => [
                        'href' => route('vehicles.index'),
                        'name' => trans('common.back')
                    ]
                ])
            </div>
        </div>

        @include('partials.spacer', ['size' => 'md'])
    </div>

@endsection