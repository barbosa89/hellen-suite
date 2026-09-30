<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ trans('payments.voucher.title') }} {{ $voucher['code'] }}</title>
    <style>
        @page { margin: 18mm 17mm 16mm; }
        * { font-family: DejaVu Sans, sans-serif; }
        body { margin: 0; color: #212121; font-size: 10px; line-height: 1.45; }
        table { width: 100%; border-collapse: collapse; }
        td, th { vertical-align: top; }
        .header { border-top: 4px solid #00bcd4; padding-top: 13px; }
        .logo-cell { width: 64px; padding-right: 12px; }
        .logo { display: block; max-width: 54px; max-height: 54px; }
        .hotel-name { margin: 0 0 3px; color: #141414; font-size: 20px; line-height: 1.15; }
        .muted { color: #595959; }
        .document-meta { width: 185px; text-align: right; }
        .document-title { margin: 0; color: #00838f; font-size: 15px; }
        .document-code { margin-top: 5px; color: #141414; font-size: 18px; font-weight: 700; }
        .notice { margin: 17px 0; padding: 9px 12px; border: 1px solid #9be7f0; border-radius: 6px; background: #e9fbfd; color: #164e54; }
        .info-table { margin-bottom: 18px; }
        .info-table td { width: 50%; padding: 12px 14px; border: 1px solid #dfdfdf; }
        .info-table td + td { border-left: 0; }
        .section-title { margin: 0 0 7px; color: #141414; font-size: 12px; font-weight: 700; }
        .label { color: #595959; font-size: 8px; font-weight: 700; text-transform: uppercase; }
        .value { margin-top: 2px; color: #212121; font-size: 10px; }
        .folio { margin-top: 16px; page-break-inside: auto; }
        .folio-heading { padding-bottom: 6px; border-bottom: 1px solid #9e9e9e; }
        .folio-name { font-size: 12px; font-weight: 700; }
        .applied { margin-left: 6px; padding: 2px 5px; border-radius: 4px; background: #e9fbfd; color: #006064; font-size: 8px; font-weight: 700; }
        .service-table { margin-top: 7px; table-layout: fixed; }
        .service-table thead { display: table-header-group; }
        .service-table tr { page-break-inside: avoid; }
        .service-table th { padding: 6px; border-bottom: 1px solid #9e9e9e; color: #595959; font-size: 8px; text-align: left; }
        .service-table td { padding: 7px 6px; border-bottom: 1px solid #ededed; overflow-wrap: break-word; }
        .service-table .number { text-align: right; white-space: nowrap; }
        .service-table .description { width: 36%; }
        .service-table .period { width: 22%; }
        .service-table .quantity { width: 10%; text-align: center; }
        .service-table .amount { width: 16%; }
        .folio-totals { margin-top: 8px; text-align: right; }
        .folio-totals span { margin-left: 14px; }
        .adjustments { margin-top: 8px; padding: 8px 10px; background: #f8f8f8; }
        .adjustment-row { padding: 2px 0; }
        .summary { width: 270px; margin: 20px 0 0 auto; }
        .summary td { padding: 5px 0 5px 12px; border-bottom: 1px solid #ededed; }
        .summary .number { text-align: right; white-space: nowrap; }
        .summary .balance td { border-bottom: 0; color: #006064; font-weight: 700; }
        .payment { margin-top: 20px; padding: 14px 16px; border: 1px solid #bdbdbd; border-radius: 8px; page-break-inside: avoid; }
        .payment-table td { vertical-align: middle; }
        .payment-amount { width: 190px; text-align: right; color: #006064; font-size: 24px; font-weight: 700; white-space: nowrap; }
        .payment-details { margin-top: 10px; }
        .payment-details td { width: 25%; padding-right: 12px; }
        .comment { margin-top: 12px; padding-top: 9px; border-top: 1px solid #ededed; }
        .footer { margin-top: 22px; padding-top: 9px; border-top: 1px solid #dfdfdf; color: #595959; font-size: 8px; text-align: center; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            @if ($voucher['hotel']['logo'])
                <td class="logo-cell"><img class="logo" src="{{ $voucher['hotel']['logo'] }}" alt=""/></td>
            @endif
            <td>
                <h1 class="hotel-name">{{ $voucher['hotel']['business_name'] }}</h1>
                <div class="muted">{{ trans('payments.voucher.tin') }}: {{ $voucher['hotel']['tin'] }}</div>
                @if ($voucher['hotel']['address'])<div class="muted">{{ $voucher['hotel']['address'] }}</div>@endif
                <div class="muted">
                    {{ collect([$voucher['hotel']['phone'], $voucher['hotel']['mobile'], $voucher['hotel']['email']])->filter()->join(' · ') }}
                </div>
            </td>
            <td class="document-meta">
                <h2 class="document-title">{{ trans('payments.voucher.title') }}</h2>
                <div class="document-code">{{ $voucher['code'] }}</div>
                <div class="muted">{{ $voucher['payment']['paid_at'] }}</div>
            </td>
        </tr>
    </table>

    <div class="notice">{{ trans('payments.voucher.non_fiscal_notice') }}</div>

    <table class="info-table">
        <tr>
            <td>
                <h3 class="section-title">{{ trans('payments.voucher.responsible') }}</h3>
                <div class="value"><strong>{{ $voucher['responsible']['name'] }}</strong></div>
                <div class="value">{{ strtoupper($voucher['responsible']['identification_type']) }} {{ $voucher['responsible']['identification_number'] }}</div>
                <div class="value muted">{{ collect([$voucher['responsible']['mobile'], $voucher['responsible']['email']])->filter()->join(' · ') }}</div>
            </td>
            <td>
                <h3 class="section-title">{{ trans('payments.voucher.stay') }} #{{ $voucher['stay']['id'] }}</h3>
                <div class="label">{{ trans('payments.voucher.check_in') }}</div>
                <div class="value">{{ $voucher['stay']['checked_in_at'] }}</div>
                <div class="label" style="margin-top: 6px;">{{ trans('payments.voucher.check_out') }}</div>
                <div class="value">{{ $voucher['stay']['checked_out_at'] }}</div>
            </td>
        </tr>
    </table>

    <h2 class="section-title">{{ trans('payments.voucher.services') }}</h2>

    @foreach ($voucher['folios'] as $folio)
        <section class="folio">
            <div class="folio-heading">
                <span class="folio-name">{{ $folio['label'] }}</span>
                <span class="muted">· {{ $folio['rooms_text'] }}</span>
                @if ($folio['is_payment_folio'])<span class="applied">{{ trans('payments.voucher.payment_applied_here') }}</span>@endif
            </div>

            <table class="service-table">
                <thead>
                    <tr>
                        <th class="description">{{ trans('payments.voucher.description') }}</th>
                        <th class="period">{{ trans('payments.voucher.period') }}</th>
                        <th class="quantity">{{ trans('payments.voucher.quantity') }}</th>
                        <th class="amount number">{{ trans('payments.voucher.unit_amount') }}</th>
                        <th class="amount number">{{ trans('payments.voucher.total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($folio['charges'] as $charge)
                        <tr>
                            <td>{{ $charge['type'] === 'lodging' ? trans('payments.charges.lodging') : $charge['description'] }}</td>
                            <td>{{ collect([$charge['service_start_on'], $charge['service_end_on']])->filter()->join(' – ') ?: '—' }}</td>
                            <td class="quantity">{{ $charge['quantity'] }}</td>
                            <td class="number">{{ $charge['unit_amount'] }}</td>
                            <td class="number"><strong>{{ $charge['total_amount'] }}</strong></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="muted">{{ trans('payments.voucher.no_services') }}</td></tr>
                    @endforelse
                </tbody>
            </table>

            @if (count($folio['adjustments']))
                <div class="adjustments">
                    @foreach ($folio['adjustments'] as $adjustment)
                        <div class="adjustment-row">
                            <strong>{{ trans("payments.adjustments.{$adjustment['type']}") }}:</strong>
                            {{ $adjustment['reason'] }} · {{ $adjustment['direction'] === 'credit' ? '−' : '+' }}{{ $adjustment['amount'] }}
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="folio-totals">
                <span>{{ trans('payments.voucher.folio_charges') }}: <strong>{{ $folio['charge_total'] }}</strong></span>
                <span>{{ trans('payments.voucher.folio_adjustments') }}: <strong>{{ $folio['adjustment_total'] }}</strong></span>
            </div>
        </section>
    @endforeach

    <table class="summary">
        <tr><td>{{ trans('payments.voucher.total_services') }}</td><td class="number">{{ $voucher['totals']['charges'] }}</td></tr>
        <tr><td>{{ trans('payments.voucher.total_adjustments') }}</td><td class="number">{{ $voucher['totals']['adjustments'] }}</td></tr>
        <tr><td>{{ trans('payments.voucher.total_paid') }}</td><td class="number">{{ $voucher['totals']['paid'] }}</td></tr>
        <tr class="balance"><td>{{ trans('payments.voucher.final_balance') }}</td><td class="number">{{ $voucher['totals']['balance'] }}</td></tr>
    </table>

    <section class="payment">
        <table class="payment-table">
            <tr>
                <td>
                    <div class="label">{{ trans('payments.voucher.payment_received') }}</div>
                    <div class="value"><strong>{{ trans("payments.methods.{$voucher['payment']['method']}") }}</strong> · {{ $voucher['payment']['currency'] }}</div>
                </td>
                <td class="payment-amount">{{ $voucher['payment']['amount'] }}</td>
            </tr>
        </table>
        <table class="payment-details">
            <tr>
                <td><div class="label">{{ trans('payments.voucher.date') }}</div><div class="value">{{ $voucher['payment']['paid_at'] }}</div></td>
                <td><div class="label">{{ trans('payments.voucher.folio') }}</div><div class="value">#{{ $voucher['payment']['folio_id'] }}</div></td>
                <td><div class="label">{{ trans('payments.voucher.recorded_by') }}</div><div class="value">{{ $voucher['payment']['recorded_by'] ?: trans('payments.voucher.system') }}</div></td>
                <td><div class="label">{{ trans('payments.voucher.support') }}</div><div class="value">{{ $voucher['payment']['has_support'] ? trans('payments.voucher.attached') : trans('payments.voucher.not_attached') }}</div></td>
            </tr>
        </table>
        @if ($voucher['payment']['comment'])
            <div class="comment"><span class="label">{{ trans('payments.fields.comment') }}</span><div class="value">{{ $voucher['payment']['comment'] }}</div></div>
        @endif
    </section>

    <footer class="footer">{{ trans('payments.voucher.footer', ['code' => $voucher['code']]) }}</footer>
</body>
</html>
