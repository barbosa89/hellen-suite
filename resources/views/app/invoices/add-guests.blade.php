@extends('layouts.app')

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('invoices.title'),
            'url' => route('invoices.index'),
            'options' => [
                [
                    'option' => trans('invoices.registerGuests'),
                    'url' => route('invoices.guests.search', ['room' => Hashids::encode($invoice->id)])
                ],
                [
                    'option' => trans('invoices.see'),
                    'url' => route('invoices.show', [
                        'id' => Hashids::encode($invoice->id)
                    ])
                ],
                [
                    'option' => trans('common.back'),
                    'url' => url()->previous()
                ],
            ]
        ])

        @include('app.invoices.info')

        @include('partials.spacer', ['size' => 'xs'])

        <div class="row">
                <div class="col-md-12">
                    @include('partials.form', [
                        'title' => [
                            'title' => trans('guests.guest'),
                        ],
                        'url' => route('invoices.guests.add', ['id' => Hashids::encode($invoice->id)]),
                        'fields' => [
                            'app.invoices.guests.info',
                            'app.invoices.guests.create-fields'
                        ],
                        'btn' => trans('common.register')
                    ])
                </div>
            </div>

        @include('partials.spacer', ['size' => 'md'])
    </div>

@endsection