@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('invoice', $voucher) }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('vouchers.title'),
            'url' => route('invoices.index'),
            'options' => [
                [
                    'option' => trans('common.create') . ' ' . trans('companies.company'),
                    'url' => route('invoices.companies.create', [
                        'id' => Hashids::encode($voucher->id)
                    ])
                ],
                [
                    'option' => trans('vouchers.see'),
                    'url' => route('invoices.show', [
                        'id' => Hashids::encode($voucher->id)
                    ])
                ]
            ]
        ])

        @include('app.invoices.info')

        <div class="hide" id="invoice" data-id="{{ Hashids::encode($voucher->id) }}"></div>

        @include('partials.spacer', ['size' => 'md'])

        <div class="row">
            <div class="col-md-12">
                @include('partials.form', [
                    'title' => [
                        'title' => trans('common.search') . ' ' . trans('companies.title'),
                        'align' => 'text-center',
                        'size' => 'h3'
                    ],
                    'url' => '#',
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
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                        <h5>@lang('common.name')</h5>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                        <h5>@lang('companies.tin')</h5>
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
        function search (str) {
            const url = '{{ url('companies/search') }}'
            const uri = "?query=" + str + "&format=rendered&template=invoices"

            if (str.length == 0) {
                $('#list').hide();
                $('#item-search').empty()
            }

            if (str.length >= 3) {
                $.ajax({
                    type: 'GET',
                    url: url + uri,
                    data: {
                        query: str
                    },
                    success: function(result) {
                        let companies = Array.from(result.companies);
                        if (companies.length) {
                            $('#item-search').empty()

                            for (let index = 0; index < companies.length; index++) {
                                $('#item-search').append($(companies[index]));
                            }

                            $('#list').show();
                        }
                    },
                    error: function(xhr){
                        toastr.error(
                            'Ha ocurrido un error',
                            'Error'
                        )
                    }
                })
            }
        }


        function add(el, e) {
            e.preventDefault()

            const voucher = $('#voucher').data('id')
            const company = el.dataset.value
            const url = '/vouchers/'+ voucher +'/companies/' + company

            window.location.replace(url)
        }
    </script>
@endsection