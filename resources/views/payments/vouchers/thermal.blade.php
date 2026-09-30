<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ trans('payments.voucher.title') }} {{ $voucher['code'] }}</title>
    <style>
        @page { margin: 5mm; }
        * { font-family: DejaVu Sans, sans-serif; }
        body { margin: 0; color: #212121; font-size: 8px; line-height: 1.35; }
        .header { padding-top: 7px; border-top: 3px solid #00bcd4; text-align: center; }
        .logo { display: block; max-width: 38px; max-height: 38px; margin: 0 auto 6px; }
        h1 { margin: 0; color: #141414; font-size: 13px; line-height: 1.15; }
        h2 { margin: 2px 0 0; color: #00838f; font-size: 10px; }
        .muted { color: #595959; }
        .code { margin-top: 4px; font-size: 12px; font-weight: 700; }
        .notice { margin: 9px 0; padding: 6px; border: 1px solid #9be7f0; background: #e9fbfd; color: #164e54; text-align: center; }
        .section { padding: 8px 0; border-top: 1px dashed #9e9e9e; page-break-inside: avoid; }
        .section-title { margin-bottom: 5px; color: #141414; font-size: 9px; font-weight: 700; }
        .label { color: #595959; font-size: 7px; font-weight: 700; text-transform: uppercase; }
        .value { margin-top: 1px; }
        .row { margin-top: 5px; }
        .folio { padding: 8px 0; border-top: 1px solid #bdbdbd; }
        .folio-title { font-size: 9px; font-weight: 700; }
        .applied { color: #006064; font-size: 7px; font-weight: 700; }
        .service { padding: 5px 0; border-bottom: 1px dotted #dfdfdf; page-break-inside: avoid; }
        .service-title { font-weight: 700; }
        .service-meta { margin-top: 2px; color: #595959; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; }
        .right { text-align: right; white-space: nowrap; }
        .adjustment { padding: 4px 0; color: #424242; }
        .totals { margin-top: 7px; }
        .totals td { padding: 3px 0; }
        .totals .final td { padding-top: 5px; border-top: 1px solid #9e9e9e; color: #006064; font-weight: 700; }
        .payment { margin: 10px 0; padding: 9px 7px; border: 1px solid #9e9e9e; text-align: center; page-break-inside: avoid; }
        .payment-amount { margin: 4px 0; color: #006064; font-size: 18px; font-weight: 700; }
        .footer { padding-top: 8px; border-top: 1px dashed #9e9e9e; color: #595959; font-size: 7px; text-align: center; }
    </style>
</head>
<body>
    <header class="header">
        @if ($voucher['hotel']['logo'])<img class="logo" src="{{ $voucher['hotel']['logo'] }}" alt=""/>@endif
        <h1>{{ $voucher['hotel']['business_name'] }}</h1>
        <div class="muted">{{ trans('payments.voucher.tin') }}: {{ $voucher['hotel']['tin'] }}</div>
        @if ($voucher['hotel']['address'])<div class="muted">{{ $voucher['hotel']['address'] }}</div>@endif
        <div class="muted">{{ collect([$voucher['hotel']['phone'], $voucher['hotel']['mobile']])->filter()->join(' · ') }}</div>
        <h2>{{ trans('payments.voucher.title') }}</h2>
        <div class="code">{{ $voucher['code'] }}</div>
    </header>

    <div class="notice">{{ trans('payments.voucher.non_fiscal_short') }}</div>

    <section class="section">
        <div class="section-title">{{ trans('payments.voucher.responsible') }}</div>
        <div><strong>{{ $voucher['responsible']['name'] }}</strong></div>
        <div>{{ strtoupper($voucher['responsible']['identification_type']) }} {{ $voucher['responsible']['identification_number'] }}</div>
    </section>

    <section class="section">
        <div class="section-title">{{ trans('payments.voucher.stay') }} #{{ $voucher['stay']['id'] }}</div>
        <div class="row"><span class="label">{{ trans('payments.voucher.check_in') }}</span><br>{{ $voucher['stay']['checked_in_at'] }}</div>
        <div class="row"><span class="label">{{ trans('payments.voucher.check_out') }}</span><br>{{ $voucher['stay']['checked_out_at'] }}</div>
    </section>

    @foreach ($voucher['folios'] as $folio)
        <section class="folio">
            <div class="folio-title">{{ $folio['label'] }}</div>
            <div class="muted">{{ $folio['rooms_text'] }}</div>
            @if ($folio['is_payment_folio'])<div class="applied">{{ trans('payments.voucher.payment_applied_here') }}</div>@endif

            @forelse ($folio['charges'] as $charge)
                <div class="service">
                    <table><tr>
                        <td><div class="service-title">{{ $charge['type'] === 'lodging' ? trans('payments.charges.lodging') : $charge['description'] }}</div></td>
                        <td class="right"><strong>{{ $charge['total_amount'] }}</strong></td>
                    </tr></table>
                    <div class="service-meta">
                        {{ $charge['quantity'] }} × {{ $charge['unit_amount'] }}
                        @if ($charge['service_start_on']) · {{ $charge['service_start_on'] }} – {{ $charge['service_end_on'] }}@endif
                    </div>
                </div>
            @empty
                <div class="service muted">{{ trans('payments.voucher.no_services') }}</div>
            @endforelse

            @foreach ($folio['adjustments'] as $adjustment)
                <div class="adjustment">
                    {{ trans("payments.adjustments.{$adjustment['type']}") }} · {{ $adjustment['direction'] === 'credit' ? '−' : '+' }}{{ $adjustment['amount'] }}<br>
                    <span class="muted">{{ $adjustment['reason'] }}</span>
                </div>
            @endforeach
        </section>
    @endforeach

    <table class="totals">
        <tr><td>{{ trans('payments.voucher.total_services') }}</td><td class="right">{{ $voucher['totals']['charges'] }}</td></tr>
        <tr><td>{{ trans('payments.voucher.total_adjustments') }}</td><td class="right">{{ $voucher['totals']['adjustments'] }}</td></tr>
        <tr><td>{{ trans('payments.voucher.total_paid') }}</td><td class="right">{{ $voucher['totals']['paid'] }}</td></tr>
        <tr class="final"><td>{{ trans('payments.voucher.final_balance') }}</td><td class="right">{{ $voucher['totals']['balance'] }}</td></tr>
    </table>

    <section class="payment">
        <div class="label">{{ trans('payments.voucher.payment_received') }}</div>
        <div class="payment-amount">{{ $voucher['payment']['amount'] }}</div>
        <div><strong>{{ trans("payments.methods.{$voucher['payment']['method']}") }}</strong> · {{ $voucher['payment']['paid_at'] }}</div>
        <div class="muted">{{ trans('payments.voucher.folio') }} #{{ $voucher['payment']['folio_id'] }}</div>
        @if ($voucher['payment']['comment'])<div class="row">{{ $voucher['payment']['comment'] }}</div>@endif
        <div class="row muted">{{ trans('payments.voucher.recorded_by') }}: {{ $voucher['payment']['recorded_by'] ?: trans('payments.voucher.system') }}</div>
    </section>

    <footer class="footer">{{ trans('payments.voucher.footer', ['code' => $voucher['code']]) }}</footer>
</body>
</html>
