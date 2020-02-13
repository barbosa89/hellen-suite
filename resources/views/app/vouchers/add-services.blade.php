@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('invoice', $voucher) }}
@endsection

@section('content')

    @include('partials.page-header', [
        'title' => trans('vouchers.title'),
        'url' => route('invoices.index'),
        'options' => [
            [
                'type' => 'hideable',
                'option' => trans('vouchers.loadProducts'),
                'url' => route('invoices.products', ['id' => Hashids::encode($voucher->id)]),
                'show' => !$voucher->reservation
            ],
            [
                'option' => 'Volver al recibo',
                'url' => route('invoices.show', [
                    'id' => Hashids::encode($voucher->id)
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
        'url' => route('invoices.services.add', ['id' => Hashids::encode($voucher->id)]),
        'fields' => [
            'app.invoices.services.add-fields',
            'app.invoices.total'
        ],
        'btn' => trans('common.add'),
        'link' => [
            'href' => route('invoices.show', ['id' => Hashids::encode($voucher->id)]),
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