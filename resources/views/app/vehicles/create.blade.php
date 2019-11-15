@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('vehicles') }}
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
                        'title' => trans('common.creationOf') . ' ' . trans('vehicles.title'),
                        'align' => 'text-center'
                    ],
                    'url' => route('vehicles.store'),
                    'fields' => [
                        'app.vehicles.create-fields',
                    ],
                    'btn' => trans('common.create'),
                    'link' => [
                        'name' => trans('common.back'),
                        'href' => route('vehicles.index')
                    ]
                ])
            </div>
        </div>

        @include('partials.spacer', ['size' => 'md'])
    </div>

@endsection