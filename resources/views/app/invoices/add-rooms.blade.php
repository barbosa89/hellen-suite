@extends('layouts.app')

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('invoices.title'),
            'url' => route('invoices.index'),
            'options' => [
                [
                    'option' => trans('invoices.registerGuests'),
                    'url' => route('invoices.guests.search', ['id' => Hashids::encode($invoice->id)])
                ],
                [
                    'option' => trans('common.back'),
                    'url' => url()->previous()
                ],
            ]
        ])

        @include('app.invoices.info')

        @include('partials.spacer', ['size' => 'md'])

        <div class="row">
            <div class="col-md-12">
                @include('partials.form', [
                    'title' => [
                        'title' => trans('rooms.addRoom'),
                        'align' => 'text-center',
                        'size' => 'h3'
                    ],
                    'url' => route('invoices.rooms.add', ['id' => Hashids::encode($invoice->id)]),
                    'fields' => [
                        'app.invoices.rooms.add-fields',
                    ],
                    'btn' => trans('common.add')
                ])
            </div>
        </div>

        @include('partials.spacer', ['size' => 'md'])
    </div>

@endsection