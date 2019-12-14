<div class="values">
    {{-- <div class="row">
        <div class="col-xs-12 mx-2">
            <div class="line"></div>
        </div>
    </div> --}}
    <div class="row mt-2 list-content">
        <div class="col-xs-10 font-weight-bold">
            SUBTOTAL
        </div>
        <div class="col-xs-2 text-right font-weight-bold">$ {{ $invoice->subvalue }} </div>
    </div>
    <div class="row mt-2 list-content">
        <div class="col-xs-10">
            @lang('common.discount')
        </div>
        <div class="col-xs-2 text-right">{{ $invoice->discount > 0 ? '-($ ' . $invoice->discount . ')' : '$ ' . $invoice->discount }}</div>
    </div>
    <div class="row mt-2 list-content">
        <div class="col-xs-10">
            @lang('common.taxes')
        </div>
        <div class="col-xs-2 text-right">$ {{ $invoice->taxes }}</div>
        <div class="col-xs-12 mx-2">
            <div class="line-end"></div>
        </div>
    </div>
    <div class="row mt-2 list-content">
        <div class="col-xs-9">
            <h3 class="font-weight-bold">TOTAL</h3>
        </div>
        <div class="col-xs-3 text-right">
            <h3 class="font-weight-bold">$ {{ $invoice->value }}</h3>
        </div>
    </div>
</div>