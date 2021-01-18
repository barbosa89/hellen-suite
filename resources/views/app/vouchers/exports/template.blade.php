<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@lang('common.invoice')</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Styles -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
    <style>
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

        .row-equal {
            display: table;
        }

        .row-equal .col-equal {
            float: none;
            display: table-cell;
            vertical-align: top;
        }
    </style>
</head>
<body>
    @foreach ($pages as $page)
        <div id="app" class="container voucher page">
            <div class="container">
                <div class="row mt-4">
                    <div class="col-3">
                        <!-- data -->
                        @include('app.vouchers.exports.data')
                        <!-- end data -->
                    </div>

                    <div class="col-9 mt-4">
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
