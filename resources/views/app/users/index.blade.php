@extends('layouts.app')

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => 'Usuarios',
            'url' => route('users.index'),
            'options' => [
                [
                    'option' => 'Nuevo',
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
                <h2>Other content</h2>
            </div>
        </div>
    </div>

@endsection