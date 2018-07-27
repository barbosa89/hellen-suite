@extends('layouts.app')

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('invoices.title'),
            'url' => route('invoices.index'),
            'options' => [
                [
                    'option' => trans('common.options'),
                    'url' => [
                        [
                            'option' => trans('rooms.addRoom'),
                            'url' => route('invoices.rooms.add', [
                                'id' => Hashids::encode($invoice->id)
                            ])
                        ],
                        [
                            'option' => trans('invoices.registerGuests'),
                            'url' => route('invoices.guests.search', [
                                'id' => Hashids::encode($invoice->id)
                            ])
                        ]
                    ]
                ],
                [
                    'option' => trans('common.back'),
                    'url' => url()->previous()
                ],
            ]
        ])

        @include('app.invoices.info')

        @if($invoice->for_company)
            <div class="row">
                <div class="col-md-12">
                    <h3 class="page-header">@lang('invoices.customerCompany')</h3>
                    @if(empty($invoice->company))
                        <a href="#">
                            <div class="well">
                                <i class="fa fa-plus-circle"></i> @lang('common.register') {{ strtolower(trans('invoices.customerCompany')) }}
                            </div>
                        </a>
                    @else
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                            <p>{{ $invoice->company->dni }}</p>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                            <p>{{ $invoice->company->name }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <h3 class="page-header">@lang('invoices.customerGuest')</h3>
                @if(empty($invoice->guest))
                <a href="{{ route('invoices.guests.search', ['room' => Hashids::encode($invoice->id)]) }}">
                        <div class="well">
                            <i class="fa fa-plus-circle"></i> @lang('common.register') {{ strtolower(trans('invoices.customerGuest')) }}
                        </div>
                    </a>
                @else
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                        <p>{{ $invoice->guest->dni }}</p>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                        <p>{{ $invoice->guest->name . ' ' . $invoice->guest->last_name }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h3 class="page-header">@lang('rooms.title')</h3>
                @if($invoice->rooms->isEmpty())
                <a href="{{ route('invoices.rooms.add', ['room' => Hashids::encode($invoice->id)]) }}">
                        <div class="well">
                            <i class="fa fa-plus-circle"></i> @lang('rooms.addRoom')
                        </div>
                    </a>
                @else
                    Imprimir listado de rooms
                @endif
            </div>
        </div>

        @include('partials.spacer', ['size' => 'md'])
    </div>

@endsection