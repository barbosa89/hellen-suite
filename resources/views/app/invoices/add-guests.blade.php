@extends('layouts.panel')

@section('content')

    @include('partials.page-header', [
        'title' => trans('invoices.title'),
        'url' => route('invoices.index'),
        'options' => [
            [
                'option' => trans('invoices.registerGuests'),
                'url' => route('invoices.guests.search', ['room' => Hashids::encode($invoice->id)])
            ],
            [
                'type' => 'hideable',
                'option' => trans('invoices.registerCompany'),
                'url' => route('invoices.companies.search', [
                    'id' => Hashids::encode($invoice->id)
                ]),
                'show' => $invoice->for_company
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

    @include('partials.spacer', ['size' => 'md'])

@endsection