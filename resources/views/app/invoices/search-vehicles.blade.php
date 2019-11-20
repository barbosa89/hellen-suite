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
                    'option' => trans('common.new') . ' ' . trans('vehicles.vehicle'),
                    'url' => route('invoices.vehicles.create', ['id' => Hashids::encode($invoice->id)])
                ],
                [
                    'option' => 'Agregar empresa',
                    'url' => route('invoices.companies.search', [
                        'id' => Hashids::encode($invoice->id)
                    ])
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

        <div class="hide" id="invoice" data-id="{{ Hashids::encode($invoice->id) }}"></div>

        @include('partials.spacer', ['size' => 'md'])

        <div class="row">
            <div class="col-md-12">
                @include('partials.form', [
                    'title' => [
                        'title' => trans('common.search') . ' ' . trans('vehicles.title'),
                        'align' => 'text-center',
                        'size' => 'h3'
                    ],
                    'url' => '#',
                    'method' => 'GET',
                    'fields' => [
                        'app.invoices.search-field',
                    ],
                    'csrf' => false
                ])
            </div>
        </div>

        <div class="crud-list" id="list" style="display:none;">
            <div class="crud-list-heading">
                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4">
                        <h5>@lang('vehicles.registration')</h5>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4">
                        <h5>@lang('common.brand')</h5>
                    </div>
                    <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                        <h5>Color</h5>
                    </div>
                </div>
            </div>
            <div class="crud-list-items" id="item-search">

            </div>
        </div>

        @include('partials.spacer', ['size' => 'md'])
    </div>

@endsection

@section('scripts')
    <script type="text/javascript">
        function render(vehicle) {
            return `
            <a href="/invoices/{{ Hashids::encode($invoice->id) }}/vehicles/${vehicle.hash}">
                <div class="crud-list-row">
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4">
                            <p>${vehicle.registration}</p>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4">
                            <p>${vehicle.brand}</p>
                        </div>
                        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4">
                            <p>${vehicle.color}</p>
                        </div>
                    </div>
                </div>
            </a>
        `}

        function search (str, e) {
            e.preventDefault();

            if (str.length == 0) {
                $('#list').hide();
                $('#item-search').empty();
            }

            if (str.length >= 3) {
                $.ajax({
                    url: '/vehicles/search?query=' + str,
                    success: function(result) {
                        let data = JSON.parse(result.data);

                        if (data.length) {
                            $('#item-search').empty();

                            data.forEach(item => {
                                $('#item-search').append(render(vehicle));
                            });

                            $('#list').show();
                        }
                    },
                    error: function(xhr){
                        toastr.error(
                            'Ha ocurrido un error',
                            'Error'
                        );
                    }
                })
            }
        }
    </script>
@endsection