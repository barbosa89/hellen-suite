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

        <div class="row">
            <div class="col-md-12">
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
            </div>
        </div>

        @include('partials.spacer', ['size' => 'xs'])
    </div>

@endsection

@section('scripts')
    <script type="text/javascript">
        function getProductPrice () {

        }

        function showTotal (value) {
            const product = $('#product').val();
            const quantity = $('#quantity').val();
            const max = $('#product').find(':selected').data('max');

            if (!empty(product) && validate(quantity, max)) {
                $.post('{{ route('products.total') }}', 
                    {
                        product: product,
                        quantity: quantity
                    },
                    function(data, status){
                        $('#total').removeAttr('value').attr('value', data.value);
                        $('#total-input').show();
                    }
                );
            } else {
                $('#total').removeAttr('value');
                $('#total-input').hide();
            }
        }

        function empty (variable) {
            if (variable.length > 0) {
                return false;
            }

            return true;
        }

        function validate (value, max) {
            if (empty(value)) {
                return false;
            }

            if (value == 0) {
                return false;
            }

            if (value > max) {
                return false;
            }
            
            return true;
        }
    </script>
@endsection