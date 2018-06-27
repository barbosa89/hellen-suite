@extends('layouts.app')

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('users.title'),
            'url' => route('users.index'),
            'options' => [
                [
                    'option' => trans('common.new'),
                    'url' => route('users.create')
                ],
                [
                    'option' => 'Test',
                    'url' => '#'
                ],
                [
                    'option' => 'Test 1 y otro',
                    'url' => [
                        [
                            'option' => 'Page 1',
                            'url' => '#'
                        ], 
                        [
                            'option' => 'Page 1',
                            'url' => '#'
                        ],  
                        [
                            'option' => 'Page 1',
                            'url' => '#'
                        ], 
                    ]
                ]
            ]
        ])

        <div class="row">
            <div class="col-md-12">
                <ul id="myTab" class="nav nav-tabs">
                    <li class="active">
                        <a href="#active" data-toggle="tab">
                            @lang('users.active')
                        </a>
                    </li>
                    <li><a href="#inactive" data-toggle="tab">@lang('users.inactive')</a></li>
                </ul>
                <div id="myTabContent" class="tab-content">
                    @include('app.users.list', [
                        'id' => 'active',
                        'active' => true
                    ])
                    @include('app.users.list', [
                        'id' => 'inactive',
                        'active' => false
                    ])
                </div>
            </div>
        </div>
    </div>

@endsection