@extends('layouts.panel')

@section('content')

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
                        'option' => trans('invoices.loadProducts'),
                        'url' => route('invoices.products', ['id' => Hashids::encode($invoice->id)]),
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
            'title' => trans('rooms.addRoom'),
            'align' => 'text-center',
            'size' => 'h3'
        ],
        'url' => route('invoices.services.add', ['id' => Hashids::encode($invoice->id)]),
        'fields' => [
            'app.invoices.services.add-fields',
            'app.invoices.total'
        ],
        'btn' => trans('common.add')
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