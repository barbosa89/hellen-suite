@extends('layouts.panel')

@section('breadcrumbs')
    {{ Breadcrumbs::render('voucher', $voucher) }}
@endsection

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('vouchers.title'),
            'url' => route('vouchers.index'),
            'options' => [
                [
                    'option' => trans('common.new') . ' ' . strtolower(trans('guests.guest')),
                    'url' => route('vouchers.guests.create', ['id' => id_encode($voucher->id)])
                ],
                [
                    'option' => trans('vouchers.linkCompany'),
                    'url' => route('vouchers.companies.search', [
                        'id' => id_encode($voucher->id)
                    ])
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

        <div class="hide" id="voucher" data-id="{{ id_encode($voucher->id) }}"></div>

        @include('partials.spacer', ['size' => 'md'])

        <div class="row">
            <div class="col-md-12">
                @include('partials.form', [
                    'title' => [
                        'title' => trans('common.search') . ' ' . trans('guests.title'),
                        'align' => 'text-center',
                        'size' => 'h3'
                    ],
                    'url' => '#',
                    'method' => 'GET',
                    'fields' => [
                        'app.vouchers.search-field',
                    ],
                    'csrf' => false
                ])
            </div>
        </div>

        <div class="crud-list" id="list" style="display:none;">
            <div class="crud-list-heading">
                <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4">
                        <h5>@lang('common.name')</h5>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4">
                        <h5>@lang('common.idNumber')</h5>
                    </div>
                    <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4 visible-md visible-lg">
                        <h5>@lang('common.status')</h5>
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
        function search (str, e) {
            e.preventDefault();

            if (str.length == 0) {
                $('#list').hide();
                $('#item-search').empty();
            }

            if (str.length >= 3) {
                $.ajax({
                    type: 'POST',
                    url: '/guests/search/unregistered',
                    data: {
                        query: str,
                        voucher: '{{ id_encode($voucher->id) }}'
                    },
                    success: function(result) {
                        let guests = result.guests;

                        if (guests.length) {
                            $('#item-search').empty();

                            for (let index = 0; index < guests.length; index++) {
                                $('#item-search').append($(guests[index]));
                            }

                            $('#list').show();
                        }
                    },
                    error: function(xhr, status, error){
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
