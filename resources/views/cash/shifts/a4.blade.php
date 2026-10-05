<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ trans('cash.report.title') }} {{ $report['code'] }}</title>
    <style>
        @page {
            margin: 18mm;
        }

        * {
            font-family: DejaVu Sans, sans-serif;
        }

        body {
            margin: 0;
            color: #202020;
            font-size: 10px;
            line-height: 1.45;
        }

        .header {
            padding-top: 14px;
            border-top: 4px solid #00bcd4;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            vertical-align: top;
        }

        h1 {
            margin: 0;
            font-size: 21px;
        }

        .muted {
            color: #656565;
        }

        .right {
            text-align: right;
        }

        .title {
            color: #00838f;
            font-size: 15px;
            font-weight: 700;
        }

        .code {
            margin-top: 4px;
            font-size: 18px;
            font-weight: 700;
        }

        .notice {
            margin: 18px 0;
            padding: 10px 12px;
            border: 1px solid #9be7f0;
            background: #e9fbfd;
            color: #164e54;
        }

        .period {
            margin-bottom: 18px;
            padding: 13px;
            border: 1px solid #dedede;
        }

        .summary th {
            padding: 8px 6px;
            border-bottom: 1px solid #999;
            color: #595959;
            font-size: 8px;
            text-align: right;
        }

        .summary th:first-child,
        .summary td:first-child {
            text-align: left;
        }

        .summary td {
            padding: 10px 6px;
            border-bottom: 1px solid #e7e7e7;
            text-align: right;
            white-space: nowrap;
        }

        .difference {
            font-weight: 700;
            color: #006064;
        }

        .notes {
            margin-top: 22px;
            padding: 12px;
            background: #f6f6f6;
        }

        .footer {
            margin-top: 28px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            color: #666;
            text-align: center;
            font-size: 8px;
        }
    </style>
</head>

<body>
    <table class="header">
        <tr>
            <td>
                <h1>{{ $report['hotel']['business_name'] }}</h1>
                <div class="muted">NIT: {{ $report['hotel']['tin'] }}</div>
                <div class="muted">
                    {{ collect([$report['hotel']['address'], $report['hotel']['phone'], $report['hotel']['mobile']])->filter()->join(' · ') }}
                </div>
            </td>
            <td class="right">
                <div class="title">{{ trans('cash.report.title') }}</div>
                <div class="code">{{ $report['code'] }}</div>
            </td>
        </tr>
    </table>
    <div class="notice">{{ trans('cash.report.non_fiscal') }}</div>
    <div class="period"><strong>{{ trans('cash.report.period') }}</strong><br>{{ $report['opened_at'] }} →
        {{ $report['closed_at'] }}</div>
    <table class="summary">
        <thead>
            <tr>
                <th>{{ trans('cash.report.method') }}</th>
                <th>{{ trans('cash.report.currency') }}</th>
                <th>{{ trans('cash.report.opening') }}</th>
                <th>{{ trans('cash.report.inflows') }}</th>
                <th>{{ trans('cash.report.outflows') }}</th>
                <th>{{ trans('cash.report.expected') }}</th>
                <th>{{ trans('cash.report.declared') }}</th>
                <th>{{ trans('cash.report.difference') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($report['rows'] as $row)
                <tr>
                    <td>{{ trans("payments.methods.{$row['method']}") }}</td>
                    <td>{{ $row['currency'] }}</td>
                    <td>{{ $row['opening'] }}</td>
                    <td>{{ $row['inflows'] }}</td>
                    <td>{{ $row['outflows'] }}</td>
                    <td>{{ $row['expected'] }}</td>
                    <td>{{ $row['declared'] }}</td>
                    <td class="difference">{{ $row['difference'] }}</td>
            </tr>@endforeach
        </tbody>
    </table>
    @if ($report['opening_note'] || $report['closing_note'])
        <div class="notes"><strong>{{ trans('cash.report.notes') }}</strong>@if ($report['opening_note'])
        <p>{{ $report['opening_note'] }}</p>@endif @if ($report['closing_note'])
            <p>{{ $report['closing_note'] }}</p>@endif
    </div>@endif
    <div class="footer">{{ $report['code'] }} · {{ trans('cash.report.non_fiscal') }}</div>
</body>

</html>