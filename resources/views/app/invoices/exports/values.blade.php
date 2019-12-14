<div class="values">
    <div class="row mt-2 list-content">
        <div class="col-xs-8 font-weight-bold">
            SUBTOTAL
        </div>
        <div class="col-xs-4 text-right font-weight-bold">$ {{ number_format($invoice->subvalue, 2, ',', '.') }} </div>
    </div>
    <div class="row mt-2 list-content">
        <div class="col-xs-8">
            @lang('common.discount')
        </div>
        <div class="col-xs-4 text-right">{{ $invoice->discount > 0 ? '-($ ' . number_format($invoice->discount, 2, ',', '.') . ')' : '$ ' . number_format($invoice->discount, 2, ',', '.') }}</div>
    </div>
    <div class="row mt-2 list-content">
        <div class="col-xs-8">
            @lang('common.taxes')
        </div>
        <div class="col-xs-4 text-right">$ {{ number_format($invoice->taxes, 2, ',', '.') }}</div>
        <div class="col-xs-12 mx-2">
            <div class="line-end"></div>
        </div>
    </div>
    <div class="row mt-2 list-content">
        <div class="col-xs-8">
            <h3 class="font-weight-bold">TOTAL</h3>
        </div>
        <div class="col-xs-4 text-right">
            <h3 class="font-weight-bold">$ {{ number_format($invoice->value, 2, ',', '.') }}</h3>
        </div>
    </div>
</div>