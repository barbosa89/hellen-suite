<div class="col-md-12">
    <h4>@lang('invoices.registry') <small>(@lang('invoices.selectIfApplicable'))</small></h4>
</div>

@include('partials.spacer', ['size' => 'xs'])

<div class="col-md-6">
    <div class="pretty p-icon p-rotate">
        <input type="checkbox" name="reservation" value="1" />
        <div class="state p-primary">
            <i class="icon fa fa-check"></i>
            <label>@lang('invoices.reservation')</label>
        </div>
    </div>
</div>

<div class="col-md-6">
    <div class="pretty p-icon p-rotate">
        <input type="checkbox" name="for_company" value="1" />
        <div class="state p-primary">
            <i class="icon fa fa-check"></i>
            <label>@lang('invoices.forCompany')</label>
        </div>
    </div>
</div>

@include('partials.spacer', ['size' => 'xs'])

<div class="col-md-12">
    <h4>@lang('invoices.statisticalData') <small>(@lang('invoices.reason'))</small></h4>
</div>

@include('partials.spacer', ['size' => 'xs'])

<div class="col-md-6">
    <div class="pretty p-icon p-rotate">
        <input type="checkbox" name="are_tourists" value="1" />
        <div class="state p-primary">
            <i class="icon fa fa-check"></i>
            <label>@lang('invoices.tourism')</label>
        </div>
    </div>
</div>

<div class="col-md-6">
    <div class="pretty p-icon p-rotate">
        <input type="checkbox" name="for_job" value="1" />
        <div class="state p-primary">
            <i class="icon fa fa-check"></i>
            <label>@lang('invoices.job')</label>
        </div>
    </div>
</div>

<div class="clearfix"></div>
