<div class="row">
    <div class="col-xs-4 col-sm-4 col-md-4 col-md-4">
        <h3>@lang('invoices.invoice'):</h3>
        <p><a href="{{ route('invoices.show', ['id' => Hashids::encode($invoice->id)] ) }}">{{ $invoice->number }}</a></p>
    </div>
    <div class="col-xs-4 col-sm-4 col-md-4 col-md-4">
        <h3>@lang('common.value'):</h3>
        <p>{{ number_format($invoice->value, 2, ',', '.') }}</p>
    </div>
    <div class="col-xs-4 col-sm-4 col-md-4 col-md-4">
        <h3>@lang('common.reservation'):</h3>
        <p>{{ $invoice->reservation ? trans('common.yes') : trans('common.no') }}</p>
    </div>
</div>

<div class="row">
    <div class="col-xs-4 col-sm-4 col-md-4 col-md-4">
        <h3>@lang('invoices.forCompany'):</h3>
        <p>{{ $invoice->for_company ? trans('common.yes') : trans('common.no') }}</p>
    </div>
    <div class="col-xs-4 col-sm-4 col-md-4 col-md-4">
        <h3>@lang('invoices.tourism'):</h3>
        <p>{{ $invoice->are_tourists ? trans('common.yes') : trans('common.no') }}</p>
    </div>
    <div class="col-xs-4 col-sm-4 col-md-4 col-md-4">
        <h3>@lang('invoices.job'):</h3>
        <p>{{ $invoice->for_job ? trans('common.yes') : trans('common.no') }}</p>
    </div>
</div>