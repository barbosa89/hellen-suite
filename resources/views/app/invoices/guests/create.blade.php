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
                    'option' => trans('common.back'),
                    'url' => url()->previous()
                ],
            ]
        ])

        <div class="row">
            <div class="col-md-12">
                @include('partials.form', [
                    'title' => [
                        'title' => trans('common.creationOf') . ' ' . trans('guests.title'),
                        'align' => 'text-center'
                    ],
                    'url' => route('invoices.guests.store', ['id' => Hashids::encode($invoice->id)]),
                    'fields' => [
                        'app.guests.create-fields',
                        'app.invoices.guests.room-field'
                    ],
                    'btn' => trans('common.create')
                ])
            </div>
        </div>

        @include('partials.spacer', ['size' => 'md'])
    </div>

@endsection