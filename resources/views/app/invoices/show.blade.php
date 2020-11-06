@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('invoice', $invoice) }}
@endsection

@section('content')
    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('invoices.title'),
            'url' => route('invoices.index'),
            'options' => [
                [
                    'type' => $invoice->status != \App\Models\Invoice::PAID ? 'confirm' : 'hideable',
                    'option' => trans('common.delete.item'),
                    'url' => route('invoices.destroy', [
                        'invoice' => id_encode($invoice->id)
                    ]),
                    'method' => 'DELETE',
                    'show' => $invoice->status != \App\Models\Invoice::PAID
                ],
            ]
        ])

        <div class="row mb-4">
            <div class="col">
                <h5>@lang('common.number'):</h5>
                <p>{{ $invoice->number }}</p>
            </div>
            <div class="col">
                <h5>@lang('common.name'):</h5>
                <p>{{ $invoice->customer_name }}</p>
            </div>
            <div class="col">
                <h5>@lang('common.idNumber'):</h5>
                <p>{{ Str::upper($invoice->identificationType->type) }} {{ $invoice->customer_dni }}</p>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <h5>@lang('common.total'):</h5>
                <p>{{ $invoice->currency->code }} ${{ number_format($invoice->total, 2, ',', '.') }}</p>
            </div>
            <div class="col">
                <h5>@lang('common.status'):</h5>
                <p>{{ trans('invoices.status.' . Str::lower($invoice->status)) }}</p>
            </div>
            <div class="col">
                <h5>@lang('common.date'):</h5>
                <p>{{ $invoice->created_at->format('Y-m-d') }}</p>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col">
                <h2 class="border-bottom">@lang('payments.title')</h2>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col">
                @include('partials.table', [
                    'data' => $invoice->payments,
                    'headers' => [
                        trans('common.value'),
                        trans('payments.method'),
                        trans('common.status'),
                        trans('common.date')
                    ],
                    'row' => 'app.invoices.payments-row'
                ])
            </div>
        </div>

        <div class="row mt-4">
            <div class="col">
                <h2 class="border-bottom">@lang('plans.title')</h2>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col">
                @include('partials.table', [
                    'data' => $invoice->plans,
                    'headers' => [
                        trans('common.price'),
                        trans('common.months'),
                        trans('common.type')
                    ],
                    'row' => 'app.invoices.plans-row'
                ])
            </div>
        </div>

        @include('partials.modal-confirm')
    </div>

@endsection
