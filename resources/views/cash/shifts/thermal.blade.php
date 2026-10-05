<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $report['code'] }}</title>
    <style>
        @page {
            margin: 5mm;
        }

        * {
            font-family: DejaVu Sans, sans-serif;
        }

        body {
            margin: 0;
            color: #222;
            font-size: 8px;
            line-height: 1.4;
        }

        h1 {
            margin: 0;
            font-size: 13px;
        }

        .header {
            padding-top: 7px;
            border-top: 3px solid #00bcd4;
            text-align: center;
        }

        .code {
            margin: 5px 0;
            font-size: 12px;
            font-weight: 700;
        }

        .muted {
            color: #666;
        }

        .notice {
            margin: 9px 0;
            padding: 6px;
            background: #e9fbfd;
            text-align: center;
        }

        .section {
            padding: 8px 0;
            border-top: 1px dashed #999;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 2px 0;
        }

        .right {
            text-align: right;
            white-space: nowrap;
        }

        .row-title {
            margin-bottom: 4px;
            font-weight: 700;
        }

        .difference {
            color: #006064;
            font-weight: 700;
        }

        .footer {
            padding-top: 8px;
            border-top: 1px dashed #999;
            text-align: center;
            font-size: 7px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>{{ $report['hotel']['business_name'] }}</h1>
        <div class="muted">NIT: {{ $report['hotel']['tin'] }}</div>
        <div class="code">{{ trans('cash.report.title') }} {{ $report['code'] }}</div>
        <div>{{ $report['opened_at'] }}<br>→ {{ $report['closed_at'] }}</div>
    </div>
    <div class="notice">{{ trans('cash.report.non_fiscal') }}</div>
    @foreach ($report['rows'] as $row)
        <div class="section">
            <div class="row-title">{{ trans("payments.methods.{$row['method']}") }} · {{ $row['currency'] }}</div>
            <table>
                <tr>
                    <td>{{ trans('cash.report.opening') }}</td>
                    <td class="right">{{ $row['opening'] }}</td>
                </tr>
                <tr>
                    <td>{{ trans('cash.report.inflows') }}</td>
                    <td class="right">{{ $row['inflows'] }}</td>
                </tr>
                <tr>
                    <td>{{ trans('cash.report.outflows') }}</td>
                    <td class="right">{{ $row['outflows'] }}</td>
                </tr>
                <tr>
                    <td>{{ trans('cash.report.expected') }}</td>
                    <td class="right">{{ $row['expected'] }}</td>
                </tr>
                <tr>
                    <td>{{ trans('cash.report.declared') }}</td>
                    <td class="right">{{ $row['declared'] }}</td>
                </tr>
                <tr class="difference">
                    <td>{{ trans('cash.report.difference') }}</td>
                    <td class="right">{{ $row['difference'] }}</td>
                </tr>
            </table>
    </div>@endforeach
    @if ($report['closing_note'])
    <div class="section"><strong>{{ trans('cash.report.notes') }}</strong><br>{{ $report['closing_note'] }}</div>@endif
    <div class="footer">{{ $report['code'] }}</div>
</body>

</html>