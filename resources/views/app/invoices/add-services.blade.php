@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('invoice', $invoice) }}
@endsection

@section('content')

    @include('partials.page-header', [
        'title' => trans('invoices.title'),
        'url' => route('invoices.index'),
        'options' => [
            [
                'type' => 'hideable',
                'option' => trans('invoices.loadProducts'),
                'url' => route('invoices.products', ['id' => Hashids::encode($invoice->id)]),
                'show' => !$invoice->reservation
            ],
            [
                'option' => 'Volver al recibo',
                'url' => route('invoices.show', [
                    'id' => Hashids::encode($invoice->id)
                ])
            ]
        ]
    ])

    @include('app.invoices.info')

    @include('partials.spacer', ['size' => 'xs'])

    @include('partials.form', [
        'title' => [
            'title' => trans('rooms.addRoom'),
            'align' => 'text-center',
            'size' => 'h3'
        ],
        'url' => route('invoices.services.add', ['id' => Hashids::encode($invoice->id)]),
        'fields' => [
            'app.invoices.services.add-fields',
            'app.invoices.total'
        ],
        'btn' => trans('common.add'),
        'link' => [
            'href' => route('invoices.show', ['id' => Hashids::encode($invoice->id)]),
            'name' => trans('common.back')
        ]
    ])

    @include('partials.spacer', ['size' => 'xs'])
@endsection

@section('scripts')
    <script type="text/javascript">
        function showTotal () {
            const product = $('#service').val();
            const quantity = $('#quantity').val();
            const url = '{{ route('services.total') }}';

            calculateTotal(url, product, quantity);
        }
    </script>
@endsection