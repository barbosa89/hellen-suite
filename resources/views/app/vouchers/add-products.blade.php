@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('voucher', $voucher) }}
@endsection

@section('content')

    @include('partials.page-header', [
        'title' => trans('vouchers.title'),
        'url' => route('vouchers.index'),
        'options' => [
            [
                'type' => 'hideable',
                'option' => trans('vouchers.loadServices'),
                'url' => route('vouchers.services', ['id' => Hashids::encode($voucher->id)]),
                'show' => !$voucher->reservation
            ],
            [
                'option' => trans('vouchers.back'),
                'url' => route('vouchers.show', [
                    'id' => Hashids::encode($voucher->id)
                ])
            ]
        ]
    ])

    @include('app.vouchers.info')

    @include('partials.spacer', ['size' => 'xs'])

    @include('partials.form', [
        'title' => [
            'title' => trans('vouchers.loadProducts'),
            'align' => 'text-center',
            'size' => 'h3'
        ],
        'url' => route('vouchers.products.add', ['id' => Hashids::encode($voucher->id)]),
        'fields' => [
            'app.vouchers.products.add-fields',
            'app.vouchers.total'
        ],
        'btn' => trans('common.add'),
        'link' => [
            'href' => route('vouchers.show', ['id' => Hashids::encode($voucher->id)]),
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