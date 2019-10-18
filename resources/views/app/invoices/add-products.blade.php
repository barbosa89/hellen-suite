@extends('layouts.panel')

@section('content')

    @include('partials.page-header', [
        'title' => trans('invoices.title'),
        'url' => route('invoices.index'),
        'options' => [
            [
                'option' => trans('common.options'),
                'type' => 'dropdown',
                'url' => [
                    [
                        'option' => trans('rooms.addRoom'),
                        'url' => route('invoices.rooms', [
                            'id' => Hashids::encode($invoice->id)
                        ])
                    ],
                    [
                        'option' => trans('invoices.registerGuests'),
                        'url' => route('invoices.guests.search', [
                            'id' => Hashids::encode($invoice->id)
                        ])
                    ],
                    [
                        'option' => trans('invoices.loadServices'),
                        'url' => route('invoices.services', ['id' => Hashids::encode($invoice->id)]),
                    ],
                    [
                        'type' => 'divider'
                    ],
                    [
                    'option' => trans('common.close'),
                        'url' => "#"
                    ],
                    [
                        'type' => 'confirm',
                        'option' => trans('common.delete'),
                        'url' => route('invoices.destroy', [
                            'room' => Hashids::encode($invoice->id)
                        ]),
                        'method' => 'DELETE'
                    ],
                ]
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
            'title' => trans('invoices.loadProducts'),
            'align' => 'text-center',
            'size' => 'h3'
        ],
        'url' => route('invoices.products.add', ['id' => Hashids::encode($invoice->id)]),
        'fields' => [
            'app.invoices.products.add-fields',
            'app.invoices.total'
        ],
        'btn' => trans('common.add')
    ])

    @include('partials.spacer', ['size' => 'xs'])
@endsection

@section('scripts')
    <script type="text/javascript">
        function showTotal () {
            const product = $('#product').val();
            const quantity = $('#quantity').val();
            const max = $('#product').find(':selected').data('max');
            const url = '{{ route('products.total') }}';

            if (validate(quantity, max)) {
                calculateTotal(url, product, quantity);
            }
        }

        function validate (value, max) {
            if (empty(value)) {
                return false;
            }

            if (value == 0 || value == null) {
                return false;
            }

            if (value > max) {
                return false;
            }
            
            return true;
        }
    </script>
@endsection