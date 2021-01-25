<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>@lang('common.invoice')</title>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ public_path('vendor/invoices/bootstrap.min.css') }}">

    <!-- Styles -->
    <style>
        h1, .h1, h2, .h2, h3, .h3 {
            margin-top: 10px !important;
        }
        .no-padding {
            padding-right: 0 !important;
            padding-left: 0 !important;
        }

        .no-padding-left {
            padding-left: 0 !important;
        }

        .img-fluid {
            max-width: 100%;
            height: auto;
        }
        .mt-2 {
            margin-top: 10px;
        }
        .mt-4 {
            margin-top: 20px;
        }

        .mb-2 {
            margin-bottom: 20px;
        }
        .mb-4 {
            margin-bottom: 20px;
        }

        .my-2 {
            margin: 0 10px;
        }

        .my-4 {
            margin: 0 20px;
        }

        .mx-2 {
            margin: 10px 0;
        }

        .mx-4 {
            margin: 20px 0;
        }

        .py-2 {
            padding: 0 10px;
        }

        .py-4 {
            padding: 0 20px;
        }

        .d-block {
            display: block;
        }

        .font-weight-light {
            font-weight: 100;
        }

        .font-weight-bold {
            font-weight: bold;
        }

        .line {
            border-bottom: 2px solid #000;
            width: 100%;
        }

        .line-light {
            width: 100%;
            border-bottom: 1px solid #949597;
        }

        .line-end {
            width: 100%;
            border-bottom: 2px solid #f0c29e;
        }

        .data {
            min-height: 1360px;
            /* background-color: #dcdddf; */
            background-color: #ffffff;
        }

        .data .data-box {
            margin-top: 60px;
        }

        .data .data-box .data-separator {
            border-top: 1px solid #949597;
            width: 10%;
        }

        .content {
            min-height: 1360px;
            /* background-color: #f1f1f1; */
            background-color: #ffffff;
        }

        .content .signature {
            margin: 80px 0 20px 0;
        }

        .without-margin {
            margin: 0 !important;
        }

        .image {
            margin: 0 auto;
            display: block;
        }

        /* To break in pages, please use this class */
        .page
        {
            page-break-after: always;
            page-break-inside: avoid;
        }

        .row-equal {
            display: table;
        }

        .row-equal .col-equal {
            float: none;
            display: table-cell;
            vertical-align: top;
        }

        .spacer-lg {
            margin: 20px;
            display: block;
        }

        .table {
            width: 100%;
            max-width: 100%;
            margin-bottom: 1rem;
            }

            .table th,
            .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #eceeef;
            }

            .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #eceeef;
            }

            .table tbody + tbody {
            border-top: 2px solid #eceeef;
            }

            .table .table {
            background-color: #fff;
            }

            .table-sm th,
            .table-sm td {
            padding: 0.3rem;
            }

            .table-bordered {
            border: 1px solid #eceeef;
            }

            .table-bordered th,
            .table-bordered td {
            border: 1px solid #eceeef;
            }

            .table-bordered thead th,
            .table-bordered thead td {
            border-bottom-width: 2px;
            }

            .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
            }

            .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.075);
            }

            .table-active,
            .table-active > th,
            .table-active > td {
            background-color: rgba(0, 0, 0, 0.075);
            }

            .table-hover .table-active:hover {
            background-color: rgba(0, 0, 0, 0.075);
            }

            .table-hover .table-active:hover > td,
            .table-hover .table-active:hover > th {
            background-color: rgba(0, 0, 0, 0.075);
            }

            .table-success,
            .table-success > th,
            .table-success > td {
            background-color: #dff0d8;
            }

            .table-hover .table-success:hover {
            background-color: #d0e9c6;
            }

            .table-hover .table-success:hover > td,
            .table-hover .table-success:hover > th {
            background-color: #d0e9c6;
            }

            .table-info,
            .table-info > th,
            .table-info > td {
            background-color: #d9edf7;
            }

            .table-hover .table-info:hover {
            background-color: #c4e3f3;
            }

            .table-hover .table-info:hover > td,
            .table-hover .table-info:hover > th {
            background-color: #c4e3f3;
            }

            .table-warning,
            .table-warning > th,
            .table-warning > td {
            background-color: #fcf8e3;
            }

            .table-hover .table-warning:hover {
            background-color: #faf2cc;
            }

            .table-hover .table-warning:hover > td,
            .table-hover .table-warning:hover > th {
            background-color: #faf2cc;
            }

            .table-danger,
            .table-danger > th,
            .table-danger > td {
            background-color: #f2dede;
            }

            .table-hover .table-danger:hover {
            background-color: #ebcccc;
            }

            .table-hover .table-danger:hover > td,
            .table-hover .table-danger:hover > th {
            background-color: #ebcccc;
            }

            .thead-inverse th {
            color: #fff;
            background-color: #292b2c;
            }

            .thead-default th {
            color: #464a4c;
            background-color: #eceeef;
            }

            .table-inverse {
            color: #fff;
            background-color: #292b2c;
            }

            .table-inverse th,
            .table-inverse td,
            .table-inverse thead th {
            border-color: #fff;
            }

            .table-inverse.table-bordered {
            border: 0;
            }

            .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            -ms-overflow-style: -ms-autohiding-scrollbar;
            }

            .table-responsive.table-bordered {
            border: 0;
            }

            body {
                font-family: 'Courier New', Courier, monospace;
            }
    </style>
</head>
<body>
    @foreach ($pages as $page)
        <div id="app" class="container voucher page">
            <div class="container">
                <div class="row mt-4">
                    <div class="col-xs-3">
                        <!-- data -->
                        @include('app.vouchers.exports.data')
                        <!-- end data -->
                    </div>

                    <div class="col-xs-9 mt-4">
                        <!-- content -->
                        <div class="content">
                            <div class="line mb-4"></div>

                            @if ($loop->first)
                                <!-- header -->
                                @include('app.vouchers.exports.header')
                                <!-- end header -->
                            @endif

                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">@lang('common.description')</th>
                                        <th scope="col">@lang('common.price')</th>
                                        <th scope="col">@lang('common.quantity')</th>
                                        <th scope="col">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($page as $item)
                                        @switch(get_class($item))
                                            @case(\App\Models\Room::class)
                                                @include('app.vouchers.exports.row', [
                                                    'description' => trans('rooms.number', ['number' => $item->number]),
                                                    'quantity' => $item->pivot->quantity,
                                                    'price' => $item->pivot->subvalue,
                                                    'value' => $item->pivot->subvalue,
                                                ])
                                                @break
                                            @case(\App\Models\Additional::class)
                                                @include('app.vouchers.exports.row', [
                                                    'description' => $item->description,
                                                    'quantity' => 1,
                                                    'price' => $item->value,
                                                    'value' => $item->value,
                                                ])
                                                @break
                                            @default
                                            @include('app.vouchers.exports.row', [
                                                'description' => $item->description,
                                                'quantity' => $item->pivot->quantity,
                                                'price' => $item->price,
                                                'value' => $item->pivot->value
                                            ])
                                        @endswitch
                                    @endforeach
                                </tbody>
                                @if ($loop->last)
                                    <tfoot>
                                        <tr>
                                            <td colspan="3">Subtotal</td>
                                            <td>$ {{ number_format($voucher->subvalue, 2, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">@lang('common.discount')</td>
                                            <td>{{ $voucher->discount > 0 ? '-($ ' . number_format($voucher->discount, 2, ',', '.') . ')' : '$ ' . number_format($voucher->discount, 2, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">@lang('common.taxes')</td>
                                            <td>$ {{ number_format($voucher->taxes, 2, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">Total</td>
                                            <td>$ {{ number_format($voucher->value, 2, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>

                            @if ($loop->last)
                                <!-- signature -->
                                @include('app.vouchers.exports.signature')
                                <!-- end signature -->

                                <!-- gratitude -->
                                @include('app.vouchers.exports.questions')
                                <!-- end gratitude -->
                            @endif

                            <!-- pagination -->
                            <div class="vouche-pagination text-right">
                                <p class="text-muted text-right">@lang('common.page') {{ $loop->iteration }} @lang('common.of') {{ count($pages) }}</p>
                            </div>
                            <!-- end pagination -->
                        </div>
                        <!-- end content -->
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }} "></script>
</body>
</html>
