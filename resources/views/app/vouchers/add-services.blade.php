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
                'option' => trans('vouchers.loadProducts'),
                'url' => route('vouchers.products', ['id' => id_encode($voucher->id)]),
                'show' => !$voucher->reservation
            ],
            [
                'option' => trans('vouchers.back'),
                'url' => route('vouchers.show', [
                    'id' => id_encode($voucher->id)
                ])
            ]
        ]
    ])

    @include('app.vouchers.info')

    @include('partials.spacer', ['size' => 'xs'])

    @include('partials.form', [
        'title' => [
            'title' => trans('rooms.addRoom'),
            'align' => 'text-center',
            'size' => 'h3'
        ],
        'url' => route('vouchers.services.add', ['id' => id_encode($voucher->id)]),
        'fields' => [
            'app.vouchers.services.add-fields',
            'app.vouchers.total'
        ],
        'btn' => trans('common.add'),
        'link' => [
            'href' => route('vouchers.show', ['id' => id_encode($voucher->id)]),
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