@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('companies', $company) }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('companies.title'),
            'url' => route('companies.index'),
            'options' => [
                [
                    'option' => trans('common.back'),
                    'url' => route('companies.index')
                ],
            ]
        ])

        <div class="row">
            <div class="col-md-12">
                @include('partials.form', [
                    'title' => [
                        'title' => trans('common.editionOf') . ' ' . trans('companies.title'),
                        'align' => 'text-center'
                    ],
                    'url' => route('companies.update', [
                        'id' => id_encode($company->id)
                    ]),
                    'method' => 'PUT',
                    'fields' => [
                        'app.companies.edit-fields',
                    ],
                    'btn' => trans('common.update'),
                    'link' => [
                        'href' => route('companies.index'),
                        'name' => trans('common.back')
                    ]
                ])
            </div>
        </div>

        @include('partials.spacer', ['size' => 'md'])
    </div>

@endsection