<div class="row">
    <div class="col-xs-4 col-sm-4 col-md-4 col-md-4">
        <h2>@lang('invoices.invoice'):</h2>
        <p>{{ $invoice->number }}</p>
    </div>
    <div class="col-xs-4 col-sm-4 col-md-4 col-md-4">
        <h2>@lang('common.value'):</h2>
        <p>{{ number_format($invoice->value, 2, ',', '.') }}</p>
    </div>
    <div class="col-xs-4 col-sm-4 col-md-4 col-md-4">
        <h2>@lang('common.reservation'):</h2>
        <p>{{ $invoice->reservation ? trans('common.yes') : trans('common.no') }}</p>
    </div>
</div>

<div class="row">
    <div class="col-xs-4 col-sm-4 col-md-4 col-md-4">
        <h2>@lang('invoices.forCompany'):</h2>
        <p>{{ $invoice->for_company ? trans('common.yes') : trans('common.no') }}</p>
    </div>
    <div class="col-xs-4 col-sm-4 col-md-4 col-md-4">
        <h2>@lang('invoices.tourism'):</h2>
        <p>{{ $invoice->are_tourists ? trans('common.yes') : trans('common.no') }}</p>
    </div>
</div>