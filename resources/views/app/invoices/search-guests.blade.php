@extends('layouts.app')

@section('content')

    <div id="page-wrapper">
        @include('partials.page-header', [
            'title' => trans('invoices.title'),
            'url' => route('invoices.index'),
            'options' => [
                [
                    'option' => trans('common.new') . ' ' . trans('guests.guest'),
                    'url' => route('invoices.guests.create', ['id' => Hashids::encode($invoice->id)])
                ],
                [
                    'option' => trans('common.seeMore'),
                    'url' => '#'
                ],
                [
                    'option' => trans('common.back'),
                    'url' => url()->previous()
                ],
            ]
        ])

        @include('app.invoices.info')

        <div class="hide" id="invoice" data-id="{{ Hashids::encode($invoice->id) }}"></div>

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
        function searchGuests (str) {
            const url = '{{ url('guests/search') }}';
            const uri = "?query=" + str + "&status=0&format=rendered&template=invoices";

            if (str.length == 0) {
                $('#list').hide();
                $('#item-search').empty();
            }

            if (str.length >= 3) {
                $.get(url + uri, function (data, status) {
                    let guests = data.guests;
                    $('#item-search').empty();
                    
                    for (let index = 0; index < guests.length; index++) {
                        $('#item-search').append($(guests[index]));           
                    }

                    $('#list').show();
                });
            }
        }


        function addGuest(el, e) {
            e.preventDefault();
            const invoice = $('#invoice').data('id');
            const guest = el.dataset.guest;
            const url = '/invoices/'+ invoice +'/guests/' + guest;
            
            window.location.replace(url);
        }
    </script>
@endsection