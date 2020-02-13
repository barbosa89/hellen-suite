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
                'option' => trans('vouchers.loadServices'),
                'url' => route('invoices.services', ['id' => Hashids::encode($voucher->id)]),
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
            'title' => trans('vouchers.loadProducts'),
            'align' => 'text-center',
            'size' => 'h3'
        ],
        'url' => route('invoices.products.add', ['id' => Hashids::encode($voucher->id)]),
        'fields' => [
            'app.invoices.products.add-fields',
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
        function showTotal() {
            const product = $('#product').val();
            const quantity = $('#quantity').val();
            const max = $('#product').find(':selected').data('max');
            const url = '{{ route('products.total') }}';

            if(validate(quantity, max)) {
                calculateTotal(url, product, quantity);
            }

            if(quantity > max) {
                $('#quantity').val(1);
                calculateTotal(url, product, 1);
            }
        }

        function validate(value, max) {
            if(empty(value)) {
                return false;
            }

            if(value == 0 || value == null) {
                return false;
            }

            if(value > max) {
                return false;
            }

            return true;
        }
    </script>
@endsection