<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@lang('common.invoice')</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Styles -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.url') . '/css/pdf.css' }}">
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
    </style>
</head>
<body>
    @foreach ($pages as $page)
        <div id="app" class="container voucher page">
            <div class="row">
                <!-- data -->
                @include('app.vouchers.exports.data')
                <!-- end data -->

                <!-- content -->
                <div class="col-xs-8 content py-4 col-equal">
                    <div class="spacer-lg">&nbsp;</div>
                    <div class="line mb-4"></div>

                    @if ($loop->first)
                        <!-- header -->
                        @include('app.vouchers.exports.header')
                        <!-- end header -->
                    @endif

                    <!-- items -->
                    <div class="items">
                        @foreach ($page as $item)
                            @if ($item instanceof \App\Welkome\Room)
                                <div class="row mt-2 list-content">
                                    <div class="col-xs-6">
                                        <p class="without-margin">@lang('rooms.room') {{ $item->number }}</p>
                                        <p class="without-margin text-muted">
                                            <small>@lang('common.date'): {{ $item->pivot->start }} - {{ $item->pivot->end }}</small>
                                        </p>
                                    </div>
                                    <div class="col-xs-2 no-padding text-center">$ {{ number_format($item->pivot->subvalue, 2, ',', '.') }}</div>
                                    <div class="col-xs-2 text-center">{{ $item->pivot->quantity }}</div>
                                    <div class="col-xs-2 no-padding-left text-right">$ {{ number_format($item->pivot->subvalue, 2, ',', '.') }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 mx-2">
                                        <div class="line-light"></div>
                                    </div>
                                </div>
                            @endif

                            @if ($item instanceof \App\Welkome\Product)
                                <div class="row mt-2 list-content">
                                    <div class="col-xs-6">
                                        <p class="without-margin">{{ $item->description }}</p>
                                        <p class="without-margin text-muted">
                                            <small>@lang('common.date'): {{ $item->pivot->created_at->format('Y-m-d') }}</small>
                                        </p>
                                    </div>
                                    <div class="col-xs-2 no-padding text-center">$ {{ number_format($item->price, 2, ',', '.') }}</div>
                                    <div class="col-xs-2 text-center">{{ $item->pivot->quantity }}</div>
                                    <div class="col-xs-2 no-padding-left text-right">$ {{ number_format($item->pivot->value, 2, ',', '.') }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 mx-2">
                                        <div class="line-light"></div>
                                    </div>
                                </div>
                            @endif

                            @if ($item instanceof \App\Welkome\Service)
                                <div class="row mt-2 list-content">
                                    <div class="col-xs-6">
                                        <p class="without-margin">{{ $item->description }}</p>
                                        <p class="without-margin text-muted">
                                            <small>@lang('common.date'): {{ $item->pivot->created_at->format('Y-m-d') }}</small>
                                        </p>
                                    </div>
                                    <div class="col-xs-2 no-padding text-center">$ {{ number_format($item->price, 2, ',', '.') }}</div>
                                    <div class="col-xs-2 text-center">{{ $item->pivot->quantity }}</div>
                                    <div class="col-xs-2 no-padding-left text-right">$ {{ number_format($item->pivot->value, 2, ',', '.') }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 mx-2">
                                        <div class="line-light"></div>
                                    </div>
                                </div>
                            @endif

                            @if ($item instanceof \App\Welkome\Additional)
                                <div class="row mt-2 list-content">
                                    <div class="col-xs-6">
                                        <p class="without-margin">{{ $item->description }}</p>
                                        <p class="without-margin text-muted">
                                            <small>@lang('common.date'): {{ $item->created_at->format('Y-m-d') }}</small>
                                        </p>
                                    </div>
                                    <div class="col-xs-2 no-padding text-center">$ {{ number_format($item->value, 2, ',', '.') }}</div>
                                    <div class="col-xs-2 text-center">1</div>
                                    <div class="col-xs-2 no-padding-left text-right">$ {{ number_format($item->value, 2, ',', '.') }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 mx-2">
                                        <div class="line-light"></div>
                                    </div>
                                </div>
                            @endif

                            @if ($item instanceof \App\Welkome\Prop)
                                <div class="row mt-2 list-content">
                                    <div class="col-xs-6">
                                        <p class="without-margin">{{ $item->description }}</p>
                                        <p class="without-margin text-muted">
                                            <small>@lang('common.date'): {{ $item->pivot->created_at->format('Y-m-d') }}</small>
                                        </p>
                                    </div>
                                    <div class="col-xs-2 no-padding text-center">$ {{ number_format($item->price, 2, ',', '.') }}</div>
                                    <div class="col-xs-2 text-center">{{ $item->pivot->quantity }}</div>
                                    <div class="col-xs-2 no-padding-left text-right">$ {{ number_format($item->pivot->value, 2, ',', '.') }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 mx-2">
                                        <div class="line-light"></div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <!-- end items -->

                    @if ($loop->last)
                        <!-- values -->
                        @include('app.vouchers.exports.values')
                        <!-- end values -->

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
    @endforeach

    <!-- Scripts -->
    <script src="{{ config('app.url') . '/js/app.js' }} "></script>
</body>
</html>
